<?php
namespace Avalanche\Bundle\ImagineBundle\Imagine\Filter\Loader;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Avalanche\Bundle\ImagineBundle\Imagine\Filter\ChainFilter;

class ChainFilterLoader implements LoaderInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
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

            $loader = $this->container->get($loaderName);

            if (false == ($loader instanceof LoaderInterface)) {
                throw new \InvalidArgumentException('Instance of "Avalanche\\Bundle\\ImagineBundle\\Imagine\\Filter\\Loader\\LoaderInterface" expected');
            }

            $loaderOptions = is_array($loaderOptions) ? $loaderOptions : array();

            $filters[] = $loader->load($loaderOptions);
        }

        return new ChainFilter($filters);
    }
}