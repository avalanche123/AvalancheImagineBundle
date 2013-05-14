<?php

namespace Avalanche\Bundle\ImagineBundle\Templating;

use Avalanche\Bundle\ImagineBundle\Imagine\CachePathResolver;

class ImagineExtension extends \Twig_Extension
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
     * (non-PHPdoc)
     * @see Twig_Extension::getFilters()
     */
    public function getFilters()
    {
        return array(
            'apply_filter' => new \Twig_Filter_Method($this, 'applyFilter'),
        );
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
    public function applyFilter($path, $filter, $absolute = false)
    {
        $realPath = realpath($this->webRoot . $path);
        if (!is_file($realPath)) {
            return null;
        }

        return $this->cachePathResolver->getBrowserPath($path, $filter, $absolute);
    }

    /**
     * (non-PHPdoc)
     * @see Twig_ExtensionInterface::getName()
     */
    public function getName()
    {
        return 'imagine';
    }
}
