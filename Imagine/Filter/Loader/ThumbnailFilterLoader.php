<?php

namespace Avalanche\Bundle\ImagineBundle\Imagine\Filter\Loader;

use Imagine\Image\Box;
use Imagine\Filter\Basic\Thumbnail;
use Imagine\Image\ImageInterface;

class ThumbnailFilterLoader implements LoaderInterface
{
    public function load(ImageInterface $image, array $options = array())
    {
        $mode = $options['mode'] === 'inset' ?
            ImageInterface::THUMBNAIL_INSET :
            ImageInterface::THUMBNAIL_OUTBOUND;
        list($width, $height) = $options['size'];

        $size = $image->getSize();
        $origWidth = $size->getWidth();
        $origHeight = $size->getHeight();

        if (null === $width || null === $height) {
            if (null === $height) {
                $height = (int)(($width / $origWidth) * $origHeight);
            } else if (null === $width) {
                $width = (int)(($height / $origHeight) * $origWidth);
            }
        }

        if ((!empty($options['allow_upscale']) && $origWidth !== $width && $origHeight !== $height)
            || ($origWidth > $width || $origHeight > $height)
        ) {
            $filter = new Thumbnail(new Box($width, $height), $mode);
            $image = $filter->apply($image);
        }

        return $image;
    }
}
