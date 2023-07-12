<?php

/**
 * Transforms locale (e.g. en-us) to directory equivalent (e.g. en_US).
 *
 * @param  string  $locale The locale string to be converted (e.g. 'en-us').
 * @return string The converted directory format (e.g. 'en_US').
 */
function locale_to_dir(string $locale): string
{
    $localeParts = explode('-', $locale);

    return $localeParts[0].'_'.strtoupper($localeParts[1]);
}
