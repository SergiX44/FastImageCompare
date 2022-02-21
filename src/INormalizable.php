<?php

namespace SergiX44\FastImageCompare;

interface INormalizable
{
    /**
     * @param $inputImagePath
     * @param $output
     * @param $tempDir
     *
     * @return string path
     */
    public function normalize($inputImagePath, $output, $tempDir);
}
