<?php

namespace SergiX44\FastImageCompare;


use RuntimeException;

abstract class NormalizableBase implements INormalizable
{

    protected $ensuredCacheDirExists;

    /**
     * @var
     */
    private $shortClassName;

    public function __construct()
    {
        $this->shortClassName = Utils::getClassNameWithoutNamespace($this);
    }

    /**
     * @return string
     */
    private function getShortClassName()
    {
        return $this->shortClassName;
    }

    /**
     * @param $filePath
     * @return string
     */
    private function buildCachePath($filePath)
    {
        return $this->getShortClassName().DIRECTORY_SEPARATOR.$this->getCacheKey($filePath);
    }

    /**
     * @param $filePath
     * @param $temporaryDirectory
     * @return string
     */
    public function getCachedFile($filePath, $temporaryDirectory)
    {
        $dest = $temporaryDirectory.$this->buildCachePath($filePath);
        if (!$this->ensuredCacheDirExists) {
            $this->ensuredCacheDirExists = dirname($dest);
            if (!file_exists($this->ensuredCacheDirExists) && !mkdir($concurrentDirectory = $this->ensuredCacheDirExists, fileperms($temporaryDirectory)) && !is_dir($concurrentDirectory)) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
            }
        }
        return $dest.'-'.basename($filePath);
    }

}