<?php

use PHPUnit\Framework\TestCase;
use SergiX44\FastImageCompare\FastImageCompare;

class FastImageCompareTest extends TestCase
{
    public function testGetImageSize()
    {
        $ff = new FastImageCompare();
        $size = $ff->getImageSize(__DIR__.'/images/t00/test1.png');
        $this->assertTrue($size['width'] == 128);
    }

    public function testAreSimilar()
    {
        $ff = new FastImageCompare();
        $files = [
            __DIR__.'/images/t00/test1.png',
            __DIR__.'/images/t00/test1-copy.png',
        ];

        $result = $ff->areSimilar($files[0], $files[1], 0.05);
        $this->assertTrue($result);
    }

    public function testAreDifferent()
    {
        $ff = new FastImageCompare();
        $files = [
            __DIR__.'/images/t00/test1.png',
            __DIR__.'/images/t00/test2.png',
        ];

        $result = $ff->areDifferent($files[0], $files[1]);
        $this->assertTrue($result);
    }
}
