<?php

namespace SergiX44\FastImageCompare;

use imagick;

class NormalizerHistogram extends NormalizableBase
{
    /**
     * @param $inputImagePath
     * @param $output
     * @param $tempDir
     *
     * @throws \ImagickException
     *
     * @return string path
     */
    public function normalize($inputImagePath, $output, $tempDir)
    {
        $imageInstanceLeft = new imagick();
        $imageInstanceLeft->readImage($inputImagePath);
        $imageInstanceLeft->equalizeImage();
        $imageInstanceLeft->writeImage($output);
        $imageInstanceLeft->clear();
        unset($imageInstanceLeft);

        return $output;
    }

    /**
     * @param $imagePath
     *
     * @return string
     */
    public function getCacheKey($imagePath)
    {
        return md5($imagePath);
    }
}
