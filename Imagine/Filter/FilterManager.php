<?php

namespace Avalanche\Bundle\ImagineBundle\Imagine\Filter;

use Avalanche\Bundle\ImagineBundle\Imagine\Filter\Loader\LoaderInterface;
use Imagine\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FilterManager
{
    private $filters;
    private $loaders;
    private $services;

    public function __construct(array $filters = array())
    {
        $this->filters   = $filters;
        $this->loaders   = array();
        $this->services  = array();
    }

    public function addLoader($name, LoaderInterface $loader)
    {
        $this->loaders[$name] = $loader;
    }

    /**
     * @param string $name
     * @return \Avalanche\Bundle\ImagineBundle\Imagine\Filter\Loader\LoaderInterface
     * @throws \Imagine\Exception\InvalidArgumentException
     */
    public function getLoader($name)
    {
        if (!isset($this->loaders[$name])) {
            throw new InvalidArgumentException(sprintf(
                'Could not find loader for "%s" filter type', $name
            ));
        }

        return $this->loaders[$name];
    }

    /**
     * @param string $filter
     * @return \Imagine\Filter\FilterInterface
     * @throws \Imagine\Exception\InvalidArgumentException
     */
    public function getFilter($filter)
    {
        if (!isset($this->filters[$filter])) {
            throw new InvalidArgumentException(sprintf(
                'Could not find image filter "%s"', $filter
            ));
        }

        $options = $this->filters[$filter];

        if (!isset($options['type'])) {
            throw new InvalidArgumentException(sprintf(
                'Filter type for "%s" image filter must be specified', $filter
            ));
        }

        if (!isset($options['options'])) {
            throw new InvalidArgumentException(sprintf(
                'Options for filter type "%s" must be specified', $filter
            ));
        }

        return $this->getLoader($options['type'])->load($options['options']);
    }

    public function getOption($filter, $name, $default = null) {
        
        $options = $this->filters[$filter];
        
        if($options && isset($options["options"]) && isset($options["options"][$name])) 
            return $options["options"][$name];
        
        return $default;
    }
}
