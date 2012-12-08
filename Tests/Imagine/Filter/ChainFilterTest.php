<?php
namespace Avalanche\Bundle\ImagineBundle\Tests\Imagine\Filter;

use Avalanche\Bundle\ImagineBundle\Imagine\Filter\ChainFilter;

class ChainFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldBeConstructedWithRightArguments()
    {
        new ChainFilter(array());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Instance of Imagine\Filter\FilterInterface expected
     */
    public function shouldTrowIfInvalidFiltersSpecified()
    {
        new ChainFilter(array(new \stdClass()));
    }

    /**
     * @test
     */
    public function shouldApplyAllFilters()
    {
        $image = $this->createImageMock();

        $filter1 = $this->createFilterMock();
        $filter1
            ->expects($this->once())
            ->method('apply')
            ->with($image)
            ->will($this->returnValue($image))
        ;

        $filter2 = $this->createFilterMock();
        $filter2
            ->expects($this->once())
            ->method('apply')
            ->with($image)
            ->will($this->returnValue($image))
        ;

        $chainFilter = new ChainFilter(array(
            $filter1,
            $filter2,
        ));

        $result = $chainFilter->apply($image);

        $this->assertSame($image, $result);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Imagine\Image\ImageInterface
     */
    protected function createImageMock()
    {
        return $this->getMock('Imagine\\Image\\ImageInterface');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Imagine\Filter\FilterInterface
     */
    protected function createFilterMock()
    {
        return $this->getMock('Imagine\\Filter\\FilterInterface');
    }
}