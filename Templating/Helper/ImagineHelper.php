<?php

namespace Avalanche\Bundle\ImagineBundle\Templating\Helper;

use Avalanche\Bundle\ImagineBundle\Imagine\CachePathResolver;
use Symfony\Component\Templating\Helper\Helper;

class ImagineHelper extends Helper
{
    /**
     * @var Avalanche\Bundle\ImagineBundle\Imagine\CachePathResolver
     */
    private $cachePathResolver;

    private $webRoot;

    /**
     * Constructs by setting $cachePathResolver
     *
     * @param \Avalanche\Bundle\ImagineBundle\Imagine\CachePathResolver $cachePathResolver
     * @param string                                                    $webroot
     */
    public function __construct(CachePathResolver $cachePathResolver, $webRoot)
    {
        $this->cachePathResolver = $cachePathResolver;
        $this->webRoot = $webRoot;
    }

    /**
     * Gets cache path of an image to be filtered
     *
     * @param string $path
     * @param string $filter
     * @param boolean $absolute
     *
     * @return string
     */
    public function filter($path, $filter, $absolute = false)
    {
        $realPath = realpath($this->webRoot . $path);
        if (!is_file($realPath)) {
            return null;
        }

        return $this->cachePathResolver->getBrowserPath($path, $filter, $absolute);
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Templating\Helper.HelperInterface::getName()
     */
    public function getName()
    {
        return 'imagine';
    }
}
