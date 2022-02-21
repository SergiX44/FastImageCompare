<?php
/**
 * (c) Paweł Plewa <pawel.plewa@gmail.com> 2018
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 *
 */

namespace SergiX44\FastImageCompare;

use Imagick;

class NormalizerGrayScale extends NormalizableBase {
    /**
     * @param $inputImagePath
     * @param $output
     * @param $tempDir
     * @return string path
     * @throws \ImagickException
     */
    public function normalize($inputImagePath, $output, $tempDir)
    {
        $imageInstanceLeft = new imagick();
        $imageInstanceLeft->readImage($inputImagePath);
        $imageInstanceLeft->transformimagecolorspace(Imagick::COLORSPACE_GRAY);
        $imageInstanceLeft->setColorspace(Imagick::COLORSPACE_GRAY);
        $imageInstanceLeft->writeImage($output);
        $imageInstanceLeft->clear();
        unset($imageInstanceLeft);
        return $output;
    }

    /**
     * @param $imagePath
     * @return string
     */
    public function getCacheKey($imagePath)
    {
        return md5($imagePath);
    }

}