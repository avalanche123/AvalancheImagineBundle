<?php

namespace Avalanche\Bundle\ImagineBundle\Imagine;

use Avalanche\Bundle\ImagineBundle\Imagine\CachePathResolver;
use Avalanche\Bundle\ImagineBundle\Imagine\Filter\FilterManager;
use Imagine\Image\ImagineInterface;
use Symfony\Component\Filesystem\Filesystem;

class CacheManager
{
    /**
     * CacheManager constructor.
     *
     * @param CachePathResolver $cachePathResolver
     * @param ImagineInterface  $imagine
     * @param FilterManager     $filterManager
     * @param Filesystem        $filesystem
     * @param string            $webRoot
     * @param string            $sourceRoot
     */
    public function __construct(
        CachePathResolver $cachePathResolver,
        ImagineInterface $imagine,
        FilterManager $filterManager,
        Filesystem $filesystem,
        $webRoot,
        $sourceRoot
    )
    {
        $this->cachePathResolver = $cachePathResolver;
        $this->imagine           = $imagine;
        $this->filterManager     = $filterManager;
        $this->filesystem        = $filesystem;
        $this->webRoot           = $webRoot;
        $this->sourceRoot        = $sourceRoot;
    }

    /**
     * Forces image caching and returns path to cached image.
     *
     * @param string $basePath
     * @param string $path
     * @param string $filter
     *
     * @return string|null
     */
    public function cacheImage($basePath, $path, $filter)
    {
        $path = '/'.ltrim($path, '/');

        //TODO: find out why I need double urldecode to get a valid path
        $browserPath = urldecode(urldecode($this->cachePathResolver->getBrowserPath($path, $filter)));

        if (!empty($basePath) && 0 === strpos($browserPath, $basePath)) {
             $browserPath = substr($browserPath, strlen($basePath));
        }

         // if cache path cannot be determined, return 404
        if (null === $browserPath) {
            return null;
        }

        $realPath = $this->webRoot.$browserPath;
        $sourcePath = $this->sourceRoot.$path;

        // if the file has already been cached, just return path
        if (file_exists($realPath)) {
            return $realPath;
        }

        if (!file_exists($sourcePath)) {
            return null;
        }

        $dir = pathinfo($realPath, PATHINFO_DIRNAME);

        if (!is_dir($dir)) {
            if (false === $this->filesystem->mkdir($dir)) {
                throw new \RuntimeException(sprintf(
                    'Could not create directory %s', $dir
                ));
            }
        }

        // TODO: get rid of hard-coded quality and format
        $this->filterManager->getFilter($filter)
            ->apply($this->imagine->open($sourcePath))
            ->save($realPath, array(
                'quality' => $this->filterManager->getOption($filter, "quality", 100),
                'format'  => $this->filterManager->getOption($filter, "format", null)
            ))
        ;
        
        return $realPath;
    }
}
