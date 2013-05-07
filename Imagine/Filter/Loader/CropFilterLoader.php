<?php

namespace Avalanche\Bundle\ImagineBundle\Imagine\Filter\Loader;

use Imagine\Image\Box;
use Imagine\Image\Point;

use Imagine\Filter\Basic\Crop;
use Imagine\Image\ImageInterface;

class CropFilterLoader implements LoaderInterface
{
    public function load(array $options = array())
    {
        list($x, $y) = $options['start'];
        list($width, $height) = $options['size'];

        return new Crop(new Point($x, $y), new Box($width, $height));
    }
}
