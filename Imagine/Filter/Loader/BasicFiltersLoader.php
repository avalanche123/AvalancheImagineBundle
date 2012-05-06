<?php

namespace Avalanche\Bundle\ImagineBundle\Imagine\Filter\Loader;

use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Image\Color;
use Imagine\Image\ManipulatorInterface;

use Imagine\Exception\InvalidArgumentException;

class BasicFiltersLoader implements LoaderInterface
{
    public function load(array $options = array())
    {
        $type = $options['type'];
        
        $options = (isset($options['options']) ? $options['options'] : array());
        
        if ('crop' === $type) {
            
            if (!isset($options['start'])) {
                throw new InvalidArgumentException(sprintf(
                        'Option "%s" for filter type "%s" must be specified', 'start', $type
                ));
            }
            
            if (!isset($options['size'])) {
                throw new InvalidArgumentException(sprintf(
                        'Option "%s" for filter type "%s" must be specified', 'size', $type
                ));
            }
            
            list($width, $height) = $options['size'];
            list($start_x, $start_y) = $options['start'];
            return new \Imagine\Filter\Basic\Crop(new Point($start_x, $start_y), new Box($width, $height));
            
        } elseif ('resize' === $type) {
            
            if (!isset($options['size'])) {
                throw new InvalidArgumentException(sprintf(
                        'Option "%s" for filter type "%s" must be specified', 'size', $type
                ));
            }
            
            list($width, $height) = $options['size'];
            return new \Imagine\Filter\Basic\Resize(new Box($width, $height));
            
        } elseif ('rotate' === $type) {
            
            if (!isset($options['angle'])) {
                throw new InvalidArgumentException(sprintf(
                        'Option "%s" for filter type "%s" must be specified', 'angle', $type
                ));
            }
            $angle = $options['angle'];
            $background = $options['background'];

            return new \Imagine\Filter\Basic\Rotate($angle, new Color($background));
        } elseif ('flipHorizontally' === $type) {
            return new \Imagine\Filter\Basic\flipHorizontally();
        } elseif ('flipVertically' === $type) {
            return new \Imagine\Filter\Basic\flipVertically();
        } elseif ('thumbnail' === $type) {
            
            if (!isset($options['mode'])) {
                throw new InvalidArgumentException(sprintf(
                        'Option "%s" for filter type "%s" must be specified', 'mode', $type
                ));
            }
            
            if (!isset($options['size'])) {
                throw new InvalidArgumentException(sprintf(
                        'Option "%s" for filter type "%s" must be specified', 'size', $type
                ));
            }
            
            $mode = ($options['mode'] === 'inset') ? ManipulatorInterface::THUMBNAIL_INSET : ManipulatorInterface::THUMBNAIL_OUTBOUND;
    
            list($width, $height) = $options['size'];
            return new \Imagine\Filter\Basic\Thumbnail(new Box($width, $height), $mode);
        }            
        
        throw new InvalidArgumentException(sprintf(
                'Could not find filter for "%s" filter type', $type
        ));
    }
}
