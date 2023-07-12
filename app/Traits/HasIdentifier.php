<?php

namespace App\Traits;

/**
 * Trait HasIdentifier
 *
 * Trait for adding a unique identifier to a model.
 */
trait HasIdentifier
{
    /**
     * Initialize the trait.
     * Add event listeners to the model.
     */
    public static function bootHasIdentifier()
    {
        // Generate and set unique identifier when creating a new model instance
        static::creating(function ($model) {
            $model->unique_identifier = self::generateUniqueIdentifier($model);
        });
    }

    /**
     * Generate a unique identifier.
     *
     * @param  Model $model
     * @return string
     */
    protected static function generateUniqueIdentifier($model)
    {
        $uniqueIdentifier = self::formatIdentifier(self::generateRandomString(12));

        // If the unique identifier already exists for another model, generate a new one
        if (self::identifierExists($model, $uniqueIdentifier)) {
            return self::generateUniqueIdentifier($model);
        }

        return $uniqueIdentifier;
    }

    /**
     * Check if a unique identifier already exists.
     *
     * @param  Model $model
     * @param  string $uniqueIdentifier
     * @return bool
     */
    protected static function identifierExists($model, $uniqueIdentifier)
    {
        return $model->where('id', '<>', $model->id)->where('unique_identifier', $uniqueIdentifier)->exists();
    }

    /**
     * Format the unique identifier.
     *
     * @param  string $identifier
     * @return string
     */
    protected static function formatIdentifier($identifier)
    {
        return implode('-', str_split($identifier, 3));
    }

    /**
     * Generate a random string of a specified length.
     *
     * @param  int $length
     * @param  string $charset
     * @return string
     */
    protected static function generateRandomString($length, $charset = '1234567890')
    {
        $str = '';
        $count = strlen($charset);

        while ($length--) {
            $str .= $charset[mt_rand(0, $count - 1)];
        }

        return $str;
    }
}
