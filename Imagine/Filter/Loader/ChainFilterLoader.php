<?php
namespace Avalanche\Bundle\ImagineBundle\Imagine\Filter\Loader;

use Avalanche\Bundle\ImagineBundle\Imagine\Filter\FilterManager;
use Avalanche\Bundle\ImagineBundle\Imagine\Filter\ChainFilter;

class ChainFilterLoader implements LoaderInterface
{
    /**
     * @var \Avalanche\Bundle\ImagineBundle\Imagine\Filter\FilterManager
     */
    protected $filterManager;

    /**
     * @param \Avalanche\Bundle\ImagineBundle\Imagine\Filter\FilterManager $filterManager
     */
    public function __construct(FilterManager $filterManager)
    {
        $this->filterManager = $filterManager;
    }

    /**
     * {@inheritDoc}
     */
    function load(array $options = array())
    {
        if (false == isset($options['filters']) || false == is_array($options['filters'])) {
            throw new \InvalidArgumentException('Expected filters key and type of array');
        }

        if (false == $options['filters']) {
            throw new \InvalidArgumentException('At least one filter expected');
        }

        $filters = array();

        foreach ($options['filters'] as $loaderName => $loaderOptions) {

            $loader = $this->filterManager->getLoader($loaderName);

            $loaderOptions = is_array($loaderOptions) ? $loaderOptions : array();

            $filters[] = $loader->load($loaderOptions);
        }

        return new ChainFilter($filters);
    }
}