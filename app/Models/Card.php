<?php

namespace App\Models;

use App\Traits\HasSchemaAccessors;
use App\Traits\HasIdentifier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Kra8\Snowflake\HasSnowflakePrimary;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;
use Carbon\Carbon;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\IntlMoneyFormatter;
use Money\Currency;
use Money\Parser\DecimalMoneyParser;

/**
 * Class Card
 *
 * Represents a Card in the application.
 */
class Card extends Model implements HasMedia
{
    use HasFactory, HasSnowflakePrimary, InteractsWithMedia, HasSchemaAccessors, HasIdentifier, HasTranslations;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cards';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'issue_date' => 'datetime',
        'expiration_date' => 'datetime',
        'last_points_issued_at' => 'datetime',
        'last_reward_redeemed_at' => 'datetime',
        'last_view' => 'datetime',
        'meta' => 'array',
    ];

    /**
     * Allow mass assignment of a model.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Translatable fields.
     *
     * @var array
     */
    public $translatable = ['head', 'title', 'description', 'custom_rule1', 'custom_rule2', 'custom_rule3'];

    /**
     * Register media collections for the model.
     */
    public function registerMediaCollections(): void
    {
        $this->addCollectionWithConversions('logo', 300, 600);
        $this->addCollectionWithConversions('background', 960, 1024);
    }

    private function addCollectionWithConversions($collectionName, $smallDimension, $mediumDimension)
    {
        $this
            ->addMediaCollection($collectionName)
            ->singleFile()
            ->registerMediaConversions(function (Media $media) use ($smallDimension, $mediumDimension) {
                $this
                    ->addMediaConversion('sm')
                    ->fit(Manipulations::FIT_MAX, $smallDimension, $smallDimension)
                    ->keepOriginalImageFormat();

                $this
                    ->addMediaConversion('md')
                    ->fit(Manipulations::FIT_MAX, $mediumDimension, $mediumDimension)
                    ->keepOriginalImageFormat();
            });
    }

    /**
     * Retrieve the value of an attribute or a dynamically generated image URL.
     *
     * @param  string  $key The attribute key or the image key with a specific conversion.
     * @return mixed The value of the attribute or the image conversion URL.
     *
     * @throws \Illuminate\Database\Eloquent\RelationNotFoundException If the relationship is not found.
     */
    public function __get($key)
    {
        $collectionNames = ['logo', 'background'];
        foreach ($collectionNames as $collectionName) {
            if (substr($key, 0, strlen($collectionName) + 1) === $collectionName . '-') {
                return $this->getImageUrl($collectionName, substr($key, strlen($collectionName) + 1, strlen($key)));
            }
        }

        return parent::__get($key);
    }

    /**
     * Get the URL of a collection with a specific conversion.
     *
     * @param  string|null  $conversion
     * @return string|null
     */
    public function getImageUrl($collection, $conversion = '')
    {
        if ($this->getFirstMediaUrl($collection) !== '') {
            $media = $this->getMedia($collection);

            // Get the resized image URL with the specified conversion
            return $media[0]->getFullUrl($conversion);
        } else {
            return null;
        }
    }

    /**
     * Get the logo URL.
     *
     * @return string|null
     */
    public function getLogoAttribute()
    {
        return $this->getImageUrl('logo');
    }

    /**
     * Get background image.
     *
     * @return string|null
     */
    public function getBackgroundAttribute()
    {
        return $this->getImageUrl('background');
    }

    /**
     * Calculate reward points based on purchase amount.
     *
     * This method calculates the reward points a customer should receive
     * based on their purchase amount. It uses the defined points_per_currency 
     * and currency_unit_amount properties of the class to perform the calculation. 
     * If the calculated points fall below the min_points_per_purchase, it sets 
     * the points to the minimum. If they exceed the max_points_per_purchase, it sets 
     * the points to the maximum. It then returns the final calculated points.
     *
     * @param float $purchaseAmount The amount of the purchase.
     * @return int The number of reward points the customer should receive.
     */
    public function calculatePoints(float $purchaseAmount): int
    {
        // Calculate points based on purchase amount
        $pointsValue = round(($purchaseAmount / $this->currency_unit_amount) * $this->points_per_currency);

        // If points are less than the minimum, set to the minimum
        if ($pointsValue < $this->min_points_per_purchase) {
            $pointsValue = $this->min_points_per_purchase;
        }

        // If points are more than the maximum, set to the maximum
        else if ($pointsValue > $this->max_points_per_purchase) {
            $pointsValue = $this->max_points_per_purchase;
        }

        // Return calculated points
        return $pointsValue;
    }

    /**
     * Get the balance of the provided member or the authenticated member.
     *
     * This function returns the total points from the transactions of a specified member or the currently authenticated member.
     * If no member is specified or authenticated, it returns zero.
     *
     * @param Member|null $member The member whose balance should be retrieved. Defaults to null.
     * @return int The balance of the member.
     */
    public function getMemberBalance(?Member $member): int
    {
        // Determine the member_id to use for the balance calculation.
        // If a member is provided, use its id. If not, check if a member is authenticated and use their id. 
        $memberId = $member 
            ? $member->id 
            : auth('member')->id();

        // Calculate the balance if a member id was found.
        // If not, set balance to 0.
        $balance = 0;
        if ($memberId) {
            $balance = Transaction::where('member_id', $memberId)
                ->where('card_id', $this->id)
                ->where('expires_at', '>', Carbon::now())
                ->select(DB::raw('SUM(points - points_used) as balance'))
                ->pluck('balance')
                ->first();

            $balance = $balance ?? 0;
        }

        return $balance;
    }

    /**
     * Get the partner that created the card.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function partner()
    {
        return $this->belongsTo(Partner::class, 'created_by');
    }

    /**
     * Formats the given value as a currency string if it is not null.
     *
     * Uses the app's current locale and currency to format the value.
     * Note: The value is assumed to be stored as cents (i.e., 100 equals 1 dollar).
     * Null is returned if the value is null.
     *
     * @param int|null $value The value to be formatted, represented in cents.
     * @return string|null
     */
    public function parseMoney(?int $value): ?string
    {
        if ($value === null) {
            return null;
        }

        // Set up the formatting tools
        $currencies = new ISOCurrencies();
        $locale = app()->make('i18n')->language->current->locale;
        $numberFormatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);

        // Convert the stored amount (cents) to dollars, parse it to a Money object, and format it
        $moneyParser = new DecimalMoneyParser($currencies);
        $amountInDollars = $value / 100;
        $amount = $moneyParser->parse($amountInDollars, new Currency($this->currency));
        $moneyFormatter = new IntlMoneyFormatter($numberFormatter, $currencies);

        return $moneyFormatter->format($amount);
    }

    /**
     * Get the club associated with the card.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    /**
     * Get the members associated with the card.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function members()
    {
        return $this->belongsToMany(Member::class, 'card_member')->withTimestamps();
    }

    /**
     * Get the rewards associated with the card.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function rewards()
    {
        return $this->belongsToMany(Reward::class, 'card_reward')->withTimestamps();
    }

    /**
     * Get the active rewards for the card, ordered by points.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function activeRewards()
    {
        return $this->belongsToMany(Reward::class, 'card_reward')
            ->where('is_active', 1)
            ->orderBy('points', 'desc');
    }

    /**
     * Get the transactions associated with the card.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
