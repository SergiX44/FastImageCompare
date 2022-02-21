<?php

namespace SergiX44\FastImageCompare;

use DateTime;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class Utils
{
    /**
     * @param $path
     * @param null $notLastModifiedSecondsAgo
     *
     * @return array
     */
    public static function getFilesOlderBy($path, $notLastModifiedSecondsAgo = null)
    {
        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        $files = [];
        foreach ($rii as $file) /**
         * @var $file SplFileInfo
         */ {
            if (!$file->isDir()) {
                if (!is_null($notLastModifiedSecondsAgo)) {
                    $nDate = new DateTime();
                    $fDate = DateTime::createFromFormat('U', $file->getMTime());
                    if ($notLastModifiedSecondsAgo <= $nDate->getTimestamp() - $fDate->getTimestamp()) {
                        $files[] = $file->getPathname();
                    }
                } else {
                    $files[] = $file->getPathname();
                }
            }
        }

        return array_unique($files);
    }

    /**
     * @param $classOrObject
     *
     * @return string
     */
    public static function getClassNameWithoutNamespace($classOrObject)
    {
        if (is_object($classOrObject)) {
            $classOrObject = get_class($classOrObject);
        }
        $path = explode('\\', ($classOrObject));

        return array_pop($path);
    }

    /**
     * @param $filesArray
     */
    public static function removeFiles($filesArray)
    {
        foreach ($filesArray as $file) {
            @unlink($file);
        }
    }
}
