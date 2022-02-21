<?php

namespace SergiX44\FastImageCompare;

use Imagick;

class ComparatorImageMagick extends ComparableBase
{

    /**
     * Absolute Error count of the number of different pixels (0=equal)
     */
    const METRIC_AE = -1;

    /**
     * Mean absolute error    (average channel error distance)
     */
    const METRIC_MAE = -2;

    /**
     * Mean squared error     (averaged squared error distance)
     */
    const METRIC_MSE = -3;

    /**
     * (sq)root mean squared error -- IE:  sqrt(MSE)
     */
    const METRIC_RMSE = -4;

    /**
     * normalized cross correlation (1 = similar)
     */
    const METRIC_NCC = -5;

    /**
     * @see \Imagick::METRIC_* constants
     * @var int
     */
    private $metric;

    /**
     * @var bool
     */
    private $ignoreAlpha = false;

    /**
     * Creates a ImageMagick comparator instance with default metric METRIC_MAE
     * @param  int  $metric
     * @param  INormalizable[]  $normalizers
     * @param $ignoreAlpha bool
     */
    public function __construct($metric = self::METRIC_MAE, $normalizers = null, $ignoreAlpha = false)
    {
        parent::__construct();
        $this->setMetric($metric);
        $this->setIgnoreAlpha($ignoreAlpha);

        if (is_null($normalizers)) {
            $this->registerNormalizer(new NormalizerSquaredSize(8));
        } elseif (is_array($normalizers)) {
            $this->setNormalizers($normalizers);
        } elseif ($normalizers instanceof INormalizable) {
            $this->registerNormalizer($normalizers);
        }

    }

    /**
     * @param $imageLeftNormalized string
     * @param $imageRightNormalized string
     * @param $imageLeftOriginal string
     * @param $imageRightOriginal string
     * @param $enoughDifference float
     * @param $instance FastImageCompare
     * @return float percentage difference in range 0..1
     * @throws \ImagickException
     */
    public function calculateDifference($imageLeftNormalized, $imageRightNormalized, $imageLeftOriginal, $imageRightOriginal, $enoughDifference, FastImageCompare $instance)
    {
        $imageInstanceLeft = new imagick();
        $imageInstanceRight = new imagick();

        //must be set before readImage
        //fuzz is only used for AE metric but we set it always for caching purposes
        $imageInstanceLeft->SetOption('fuzz', (int) ($enoughDifference * 100).'%'); //http://www.imagemagick.org/script/command-line-options.php#define

        $imageInstanceLeft->readImage($imageLeftNormalized);
        $imageInstanceRight->readImage($imageRightNormalized);


        if ($this->isIgnoreAlpha()) {
            $imageInstanceLeft->setImageBackgroundColor('#FFFFFF');
            $imageInstanceLeft = $imageInstanceLeft->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);

            $imageInstanceRight->setImageBackgroundColor('#FFFFFF');
            $imageInstanceRight = $imageInstanceRight->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
        }


        $difference = $imageInstanceLeft->compareImages($imageInstanceRight, $this->localMetricToImagickMetric($this->getMetric()))[1];

        switch ($this->getMetric()) {
            case self::METRIC_AE:
                $difference = ($difference > 0) ? $difference / ($imageInstanceLeft->getImageWidth() * $imageInstanceLeft->getImageHeight()) : $difference;
                break;
            case self::METRIC_NCC:
                $difference = 1.0 - $difference;
                break;
        }
        $imageInstanceLeft->clear();
        $imageInstanceRight->clear();
        unset($imageInstanceLeft, $imageInstanceRight);
        return $difference;
    }


    /**
     * @return int
     */
    public function getMetric()
    {
        return $this->metric;
    }

    /**
     * @param  int  $metric
     * @see \Imagick::METRIC_*
     */
    public function setMetric($metric)
    {
        $this->metric = $metric;
    }

    private function localMetricToImagickMetric($metric)
    {
        return match ($metric) {
            self::METRIC_AE => Imagick::METRIC_ABSOLUTEERRORMETRIC,
            self::METRIC_MSE => Imagick::METRIC_MEANSQUAREERROR,
            self::METRIC_RMSE => Imagick::METRIC_ROOTMEANSQUAREDERROR,
            self::METRIC_NCC => Imagick::METRIC_NORMALIZEDCROSSCORRELATIONERRORMETRIC,
            default => Imagick::METRIC_MEANABSOLUTEERROR,
        };
    }

    /**
     * @return bool
     */
    public function isIgnoreAlpha()
    {
        return $this->ignoreAlpha;
    }

    /**
     * @param  bool  $ignoreAlpha
     */
    public function setIgnoreAlpha($ignoreAlpha)
    {
        $this->ignoreAlpha = $ignoreAlpha;
    }

    public function generateCacheKey($imageLeft, $imageRight)
    {
        return implode('-', [$this->getMetric(), $this->isIgnoreAlpha() ? 'ia' : 'iaOff']);
    }

}