<?php

namespace App\Models;

use App\QueryBuilders\PartnerQueryBuilder;
use App\Traits\HasSchemaAccessors;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\File;
use Kra8\Snowflake\HasSnowflakePrimary;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Class Partner
 *
 * Represents a Partner in the application.
 */
class Partner extends Authenticatable implements HasLocalePreference, HasMedia
{
    use HasApiTokens, Notifiable, InteractsWithMedia, HasSnowflakePrimary, HasSchemaAccessors;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'partners';

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'meta' => 'array',
    ];

    /**
     * Allow mass assignment of a model.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Append programmatically added columns.
     *
     * @var array
     */
    protected $appends = [
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function newEloquentBuilder($query)
    {
        return new PartnerQueryBuilder($query);
    }

    /**
     * Get the user's preferred locale.
     *
     * @return string
     */
    public function preferredLocale()
    {
        $locale = $this->locale;
        $defaultLocale = config('app.locale');

        return File::exists(lang_path().'/'.$locale) ? $locale : $defaultLocale;
    }

    /**
     * Check if user has a specific role.
     *
     * @param  array|string  $roles
     */
    public function hasRole($roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];

        return in_array($this->role, $roles);
    }

    /**
     * Register media collections for the model.
     */
    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('avatar')
            ->singleFile()
            ->registerMediaConversions(function (Media $media) {
                // First conversion: small
                $this
                    ->addMediaConversion('small')
                    ->fit(Manipulations::FIT_MAX, 80, 80)
                    ->keepOriginalImageFormat();

                // Second conversion: medium
                $this
                    ->addMediaConversion('medium')
                    ->fit(Manipulations::FIT_MAX, 320, 320)
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
        if (substr($key, 0, 7) === 'avatar-') {
            return $this->getAvatarUrl(substr($key, 7, strlen($key)));
        }

        return parent::__get($key);
    }

    /**
     * Get the avatar URL.
     *
     * @return string|null
     */
    public function getAvatarAttribute()
    {
        return $this->getAvatarUrl();
    }

    /**
     * Get the avatar URL with a specific conversion.
     *
     * @param  string|null  $conversion
     * @return string|null
     */
    public function getAvatarUrl($conversion = '')
    {
        if ($this->getFirstMediaUrl('avatar') !== '') {
            $media = $this->getMedia('avatar');

            // Get the resized image URL with the specified conversion
            return $media[0]->getFullUrl($conversion);
        } else {
            return null;
        }
    }

    /**
     * Get the network associated with the partner.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function network()
    {
        return $this->belongsTo(Network::class);
    }

    /**
     * Get the clubs associated with the partner.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function clubs()
    {
        return $this->hasMany(Club::class, 'created_by');
    }

    /**
     * Get the rewards associated with the partner.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rewards()
    {
        return $this->hasMany(Reward::class, 'created_by');
    }

    /**
     * Get the cards associated with the partner.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cards()
    {
        return $this->hasMany(Card::class, 'created_by');
    }

    /**
     * Get the transactions associated with the partner.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'created_by');
    }

    /**
     * Get the staff members associated with the partner.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function staff()
    {
        return $this->hasMany(Staff::class, 'created_by');
    }
}