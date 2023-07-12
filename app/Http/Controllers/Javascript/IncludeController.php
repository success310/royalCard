<?php

namespace App\Http\Controllers\Javascript;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IncludeController extends Controller
{
    /**
     * Include the lang.js file for the given locale.
     *
     * This method generates a JavaScript file containing a JSON representation of
     * the language translations array for the given locale. The generated JavaScript
     * file is returned with a 'Content-Type' header set to 'application/javascript'.
     *
     * @param string $locale The locale for which the language file should be generated.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function language(string $locale, Request $request)
    {
        // Convert the locale to its corresponding directory
        $langDirectory = locale_to_dir($locale);

        // Build the file path for the JavaScript language file
        $langFilePath = lang_path() . '/' . $langDirectory . '/javascript.php';

        // Include the language file and store the returned array
        $langFileArray = include_once $langFilePath;

        // Convert the language array to a JSON string and assign it to a JavaScript constant
        $langFileContent = 'const _lang = ' . json_encode($langFileArray) . ';';

        // Return the generated JavaScript content with the 'Content-Type' header set to 'application/javascript'
        return response($langFileContent)->header('Content-Type', 'application/javascript');
    }
}
