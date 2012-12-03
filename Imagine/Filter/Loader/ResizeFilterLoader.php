<?php

namespace Avalanche\Bundle\ImagineBundle\Imagine\Filter\Loader;

use Imagine\Filter\Basic\Resize;
use Imagine\Image\Box;

class ResizeFilterLoader implements LoaderInterface
{
    /**
     * @see Avalanche\Bundle\ImagineBundle\Imagine\Filter\Loader\LoaderInterface::load()
     */
    public function load(array $options = array())
    {
        list($width, $height) = $options['size'];

        return new Resize(new Box($width, $height));
    }
}
