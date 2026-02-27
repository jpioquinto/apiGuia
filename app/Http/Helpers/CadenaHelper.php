<?php
namespace App\Http\Helpers;

class CadenaHelper
{
    static function clearFileName(string $input)
    {
        return str_replace(
            ['Á','É','Í','Ó','Ú','Ü','á','é','í','ó','ú','ü','Ñ','ñ','#','Ã³', '°', '/'],
            ['A','E','I','O','U','U','a','e','i','o','u','u','N','n','-','o', 'o', '|'],
            $input
        );
    }

    static function extension(string $nombreDoc): string
    {
        $doc = explode('.', $nombreDoc);
        return trim( end($doc) );
    }

    static function removeExtension(string $nombreDoc): string
    {
        $doc = explode('.', $nombreDoc);
        array_pop($doc);
        return implode('.', $doc);
    }

    static function getNameToPath(string $path): string
    {
        $data = explode('/', $path);
        $fileName = array_pop($data);
        return $fileName;
    }

}
