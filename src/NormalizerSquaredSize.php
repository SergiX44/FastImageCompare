<?php

namespace SergiX44\FastImageCompare;


use Exception;
use Gumlet\ImageResize;

class NormalizerSquaredSize extends NormalizableBase
{
    private $sampleSize;

    public function __construct($sampleSize = 8)
    {
        parent::__construct();
        $this->setSampleSize(max(2, $sampleSize));
    }

    public function normalize($inputImagePath, $output, $tempDir)
    {
        try {
            $imageResize = new ImageResize($inputImagePath);
            $imageResize->quality_jpg = 100;
            $imageResize->quality_png = 9;
            $imageResize->quality_webp = 100;
            $imageResize->quality_truecolor = true;
            $imageResize->resize($this->getSampleSize(), $this->getSampleSize(), true);
            $imageResize->save($output, IMAGETYPE_PNG);
            unset($imageResize);
            return $output;
        } catch (Exception $e) {
            copy($inputImagePath, $output);
            return $output;
        }

    }

    public function getCacheKey($imagePath)
    {
        return md5($imagePath).'.n'.$this->getSampleSize().'.png';
    }

    /**
     * @return int
     */
    public function getSampleSize()
    {
        return $this->sampleSize;
    }

    /**
     * @param  int  $sampleSize
     */
    public function setSampleSize($sampleSize)
    {
        $this->sampleSize = $sampleSize;
    }

}