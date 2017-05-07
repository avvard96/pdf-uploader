<?php

namespace App\Helpers;

use League\Flysystem\Filesystem;

/**
 * Class FileHelper
 * @package App\Helpers
 */
class FileHelper {
    /**
     * Maximum acceptable file size (in megabytes)
     */
    const MAX_FILE_SIZE = 100;

    /**
     * Checks if file has pdf extension.
     *
     * @param $type
     * @return bool
     */
    public static function isPdf($type)
    {
        return (strtolower($type) == 'pdf') ? true : false;
    }

    /**
     * Checks if file has valid size.
     *
     * @param $bytes
     * @return bool
     */
    public static function isValidSize($bytes)
    {
        return ($bytes <= self::MAX_FILE_SIZE * pow(1024, 2)) ? true : false;
    }

    /**
     * Stores file in Google Drive root folder.
     *
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