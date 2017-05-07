<?php

namespace App\Helpers;

use League\Flysystem\Filesystem;

/**
 * Class FileHelper
 * @package App\Helpers
 */
class FileHelper {
    const MAX_FILE_SIZE = 100;

    /**
     * @param $type
     * @return bool
     */
    public static function isPdf($type)
    {
        return strtolower($type) == 'pdf' ? true : false;
    }

    public static function isValidSize($bytes)
    {
        return ($bytes <= self::MAX_FILE_SIZE * pow(1024, 2)) ? true : false;
    }

    /**
     * @param $fileName
     * @param $decodedContent
     * @param $driver
     * @return bool
     */
    public static function store($fileName, $decodedContent, $driver)
    {
        if ($driver instanceof Filesystem) {
            if ($driver->has($fileName)) {
                $fileName = uniqid() . $fileName;
            }
            return $driver->put($fileName, $decodedContent);
        }

        return false;
    }


}