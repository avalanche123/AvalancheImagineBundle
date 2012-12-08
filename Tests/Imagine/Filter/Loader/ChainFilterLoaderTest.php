<?php
namespace Avalanche\Bundle\ImagineBundle\Tests\Imagine\Filter\Loader;

use Avalanche\Bundle\ImagineBundle\Imagine\Filter\Loader\ChainFilterLoader;
use Symfony\Component\DependencyInjection\Container;
use Avalanche\Bundle\ImagineBundle\Tests\Imagine\Filter\Loader\Files\Filter;

class ChainFilterLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldBeConstructedWithRightArguments()
    {
        new ChainFilterLoader(new Container());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Expected filters key and type of array
     */
    public function shouldThrowIfOptionsArrayNotContainsFilterKey()
    {
        $loader = new ChainFilterLoader(new Container());
        $loader->load(array());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Expected filters key and type of array
     */
    public function shouldThrowIfFilterKeyIsNotArrayType()
    {
        $loader = new ChainFilterLoader(new Container());
        $loader->load(array('filters' => 1));
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Instance of "Avalanche\Bundle\ImagineBundle\Imagine\Filter\Loader\LoaderInterface" expected
     */
    public function shouldThrowIfLoaderHaveWrongType()
    {
        $container = new Container();
        $container->set('filter', new \stdClass());


        $loader = new ChainFilterLoader($container);
        $loader->load(array(
            'filters' => array(
                'filter' => array()
            )
        ));
    }

    /**
     * @test
     */
    public function shouldReturnChainFilter()
    {
        $filterLoader1 = $this->createFilterLoaderMock();
        $filterLoader1
            ->expects($this->once())
            ->method('load')
            ->will($this->returnValue(new Filter()))
        ;

        $filterLoader2 = $this->createFilterLoaderMock();
        $filterLoader2
            ->expects($this->once())
            ->method('load')
            ->will($this->returnValue(new Filter()))
        ;

        $container = new Container();
        $container->set('filter1', $filterLoader1);
        $container->set('filter2', $filterLoader2);


        $loader = new ChainFilterLoader($container);
        $result = $loader->load(array(
            'filters' => array(
                'filter1' => array(),
                'filter2' => array(),
            )
        ));

        $this->assertInstanceOf('Avalanche\\Bundle\\ImagineBundle\\Imagine\\Filter\\ChainFilter', $result);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Avalanche\Bundle\ImagineBundle\Imagine\Filter\Loader\LoaderInterface
     */
    protected function createFilterLoaderMock()
    {
        return $this->getMock('Avalanche\\Bundle\\ImagineBundle\\Imagine\\Filter\\Loader\\LoaderInterface');
    }
}