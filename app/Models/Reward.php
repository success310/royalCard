<?php

namespace App\Models;

use App\Traits\HasSchemaAccessors;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kra8\Snowflake\HasSnowflakePrimary;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

/**
 * Class Reward
 *
 * Represents a Reward in the application.
 * 
 * @property-read string|null $image1
 * @property-read string|null $image2
 * @property-read string|null $image3
 * @property-read string|null $image4
 * @property-read string|null $image5
 * @property-read array|null $images
 */
class Reward extends Model implements HasMedia
{
    use HasFactory, HasSnowflakePrimary, InteractsWithMedia, HasSchemaAccessors, HasTranslations;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rewards';

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
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
    public $translatable = ['title', 'description'];

    /**
     * Register media collections for the model.
     */
    public function registerMediaCollections(): void
    {
        // Define the sizes for the image conversions
        $conversions = [
            'xs' => [120, 90],
            'sm' => [480, 360],
            'md' => [1024, 768],
            //'lg' => [1280, 960],
        ];

        // Add media collections for each image
        for ($i = 1; $i <= 5; $i++) {
            $this->addMediaCollection("image{$i}")
                ->singleFile()
                ->registerMediaConversions(function (Media $media) use ($conversions) {
                    foreach ($conversions as $conversion => $sizes) {
                        $this->addMediaConversion($conversion)
                            ->fit(Manipulations::FIT_MAX, $sizes[0], $sizes[1])
                            ->keepOriginalImageFormat();
                    }
                });
        }
    }

    /**
     * Retrieve the value of an attribute or a dynamically generated image URL.
     *
     * @param  string  $key The attribute key or the image key with a specific conversion.
     * @return mixed The value of the attribute or the image conversion URL.
     */
    public function __get($key)
    {
        // Check if the key corresponds to a collection name and conversion
        for ($i = 1; $i <= 5; $i++) {
            $collectionName = "image{$i}";
            if (str_starts_with($key, "{$collectionName}-")) {
                return $this->getImageUrl($collectionName, substr($key, strlen($collectionName) + 1));
            }
        }

        return parent::__get($key);
    }

    /**
     * Get the URL of a collection with a specific conversion.
     *
     * @param  string $collection The collection name.
     * @param  string|null  $conversion The conversion name.
     * @return string|null The image URL or null if the image does not exist.
     */
    public function getImageUrl(string $collection, string $conversion = ''): ?string
    {
        // Check if the image exists
        if ($this->getFirstMediaUrl($collection) !== '') {
            $media = $this->getMedia($collection);
            // Get the resized image URL with the specified conversion
            return $media[0]->getFullUrl($conversion);
        }

        return null;
    }

    /**
     * Get array of all images.
     *
     * @return array|null The URLs of all image conversions or null if no images exist.
     */
    public function getImagesAttribute(): ?array
    {
        $images = [];
        
        for ($i = 1; $i <= 3; $i++) {
            $collectionName = "image{$i}";
            $mediaItems = $this->getMedia($collectionName);

            if (!$mediaItems->isEmpty()) {
                $mediaItem = $mediaItems->first();
                $images[] = [
                    'xs' => $mediaItem->getUrl('xs'),
                    'sm' => $mediaItem->getUrl('sm'),
                    'md' => $mediaItem->getUrl('md'),
                    'ratio' => $this->getAspectRatio($collectionName)
                ];
            }
        }

        return empty($images) ? null : $images;
    }

    /**
     * Get the image 1 URL.
     *
     * @return string|null
     */
    public function getImage1Attribute()
    {
        return $this->getImageUrl('image1');
    }

    /**
     * Get the image aspect ratio.
     *
     * @param string $image The image key.
     * @return string|null The aspect ratio in 'width / height' format, or null if the image is not found or the dimensions are invalid.
     */
    public function getAspectRatio($image)
    {
        $mediaItem = $this->getFirstMedia($image);

        if ($mediaItem) {
            $dimensions = getimagesize($mediaItem->getPath());

            if ($dimensions !== false) {
                list($width, $height) = $dimensions;
                
                if ($width && $height) {
                    // Calculate the Greatest Common Divisor (GCD) using the Euclidean algorithm
                    $gcdValue = gcd($width, $height);

                    // Return the aspect ratio in 'width / height' format
                    return ($width/$gcdValue) . " / " . ($height/$gcdValue);
                }
            }
        }

        // Return null if the image is not found or the dimensions are invalid
        return null;
    }

    /**
     * Get the aspect ratio of image1.
     *
     * @return string|null
     */
    public function getImage1AspectRatioAttribute()
    {
        return $this->getAspectRatio('image1');
    }

    /**
     * Get the aspect ratio of image2.
     *
     * @return string|null
     */
    public function getImage2AspectRatioAttribute()
    {
        return $this->getAspectRatio('image2');
    }

    /**
     * Get the aspect ratio of image3.
     *
     * @return string|null
     */
    public function getImage3AspectRatioAttribute()
    {
        return $this->getAspectRatio('image3');
    }

    /**
     * Get the image 2 URL.
     *
     * @return string|null
     */
    public function getImage2Attribute()
    {
        return $this->getImageUrl('image2');
    }

    /**
     * Get the image 3 URL.
     *
     * @return string|null
     */
    public function getImage3Attribute()
    {
        return $this->getImageUrl('image3');
    }

    /**
     * Get the image 4 URL.
     *
     * @return string|null
     */
    public function getImage4Attribute()
    {
        return $this->getImageUrl('image4');
    }

    /**
     * Get the image 5 URL.
     *
     * @return string|null
     */
    public function getImage5Attribute()
    {
        return $this->getImageUrl('image5');
    }

    /**
     * Get the partner that created the reward.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function partner()
    {
        return $this->belongsTo(Partner::class, 'created_by');
    }

    /**
     * The cards that belong to the reward.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function cards()
    {
        return $this->belongsToMany(Card::class, 'card_reward');
    }

    /**
     * The transaction associated with the reward.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }
}
