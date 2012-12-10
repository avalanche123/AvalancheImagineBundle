<?php

namespace Avalanche\Bundle\ImagineBundle\Imagine\Filter\Loader;

use Imagine\Exception\InvalidArgumentException;
use Imagine\Filter\Advanced\RelativeResize;

class RelativeResizeFilterLoader implements LoaderInterface
{
    /**
     * @see Avalanche\Bundle\ImagineBundle\Imagine\Filter\Loader\LoaderInterface::load()
     */
    public function load(array $options = array())
    {
        foreach ($options as $method => $parameter) {
            return new RelativeResize($method, $parameter);
        }

        throw new InvalidArgumentException('Expected method/parameter pair, none given');
    }
}
