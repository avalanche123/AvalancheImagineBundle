<?php
namespace Avalanche\Bundle\ImagineBundle\Imagine\Filter\Loader;

use Imagine\Image\ImagineInterface;
use Avalanche\Bundle\ImagineBundle\Imagine\Filter\PasteFilter;

class PasteFilterLoader implements LoaderInterface
{
    /**
     * @var \Imagine\Image\ImagineInterface
     */
    protected $imagine;

    /**
     * @param \Imagine\Image\ImagineInterface $imagine
     */
    public function __construct(ImagineInterface $imagine)
    {
        $this->imagine = $imagine;
    }

    /**
     * {@inheritDoc}
     */
    function load(array $options = array())
    {
        if (false == isset($options['image'])) {
            throw new \InvalidArgumentException('Option "image" is required.');
        }

        if (false == is_readable($options['image'])) {
            throw new \InvalidArgumentException('Expected image file exists and readable.');
        }

        $x = isset($options['x']) ? $options['x'] : 0;
        $y = isset($options['y']) ? $options['y'] : 0;

        $image = $this->imagine->open($options['image']);

        return new PasteFilter($image, $x, $y);
    }
}