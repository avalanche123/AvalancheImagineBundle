<?php
namespace Avalanche\Bundle\ImagineBundle\Tests\Imagine\Filter;

use Avalanche\Bundle\ImagineBundle\Imagine\Filter\PasteFilter;
use Imagine\Image\Point;
use Imagine\Image\Box;

class PasteFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldBeConstructedWithRightArguments()
    {
        new PasteFilter($this->createImageMock(), 0, 0);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Expected "x" one of the [left|right|center] or integer greater than zero
     */
    public function shouldThrowIfXNotValidString()
    {
        new PasteFilter($this->createImageMock(), 'wrong', 0);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Expected "x" one of the [left|right|center] or integer greater than zero
     */
    public function shouldThrowIfXNotValidInteger()
    {
        new PasteFilter($this->createImageMock(), -5, 0);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Expected "x" one of the [left|right|center] or integer greater than zero
     */
    public function shouldThrowIfXNotStringNotInteger()
    {
        new PasteFilter($this->createImageMock(), array(), 0);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Expected "y" one of the [top|bottom|middle] or integer greater than zero
     */
    public function shouldThrowIfYNotValidString()
    {
        new PasteFilter($this->createImageMock(), 0, 'wrong');
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Expected "y" one of the [top|bottom|middle] or integer greater than zero
     */
    public function shouldThrowIfYNotValidInteger()
    {
        new PasteFilter($this->createImageMock(), 0, -5);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Expected "y" one of the [top|bottom|middle] or integer greater than zero
     */
    public function shouldThrowIfYNotStringNotInteger()
    {
        new PasteFilter($this->createImageMock(), 0, array());
    }

    /**
     * @test
     */
    public function shouldReturnResultImage()
    {
        $expectedImage = $this->createImageMock();

        $pasteImage = $this->createImageMock();

        $image = $this->createImageMock();
        $image
            ->expects($this->once())
            ->method('paste')
            ->with($pasteImage, new Point(5, 9))
            ->will($this->returnValue($expectedImage))
        ;

        $filter = new PasteFilter($pasteImage, 5, 9);

        $result = $filter->apply($image);

        $this->assertSame($expectedImage, $result);
    }

    /**
     * @test
     */
    public function shouldConvertLeftToInteger()
    {
        $pasteImage = $this->createImageMock();
        $pasteImage
            ->expects($this->never())
            ->method('getSize')
        ;

        $image = $this->createImageMock();
        $image
            ->expects($this->once())
            ->method('paste')
            ->with($pasteImage, new Point(0, 10))
        ;
        $image
            ->expects($this->never())
            ->method('getSize')
        ;

        $filter = new PasteFilter($pasteImage, 'left', 10);

        $filter->apply($image);
    }

    /**
     * @test
     */
    public function shouldConvertRightToInteger()
    {
        $pasteImage = $this->createImageMock();
        $pasteImage
            ->expects($this->once())
            ->method('getSize')
            ->will($this->returnValue(new Box(10, 10)))
        ;

        $image = $this->createImageMock();
        $image
            ->expects($this->once())
            ->method('paste')
            ->with($pasteImage, new Point(100 - 10, 10))
        ;
        $image
            ->expects($this->once())
            ->method('getSize')
            ->will($this->returnValue(new Box(100, 100)))
        ;

        $filter = new PasteFilter($pasteImage, 'right', 10);

        $filter->apply($image);
    }

    /**
     * @test
     */
    public function shouldConvertCenterToInteger()
    {
        $pasteImage = $this->createImageMock();
        $pasteImage
            ->expects($this->once())
            ->method('getSize')
            ->will($this->returnValue(new Box(10, 10)))
        ;

        $image = $this->createImageMock();
        $image
            ->expects($this->once())
            ->method('paste')
            ->with($pasteImage, new Point((100 / 2) - (10 / 2), 10))
        ;
        $image
            ->expects($this->once())
            ->method('getSize')
            ->will($this->returnValue(new Box(100, 100)))
        ;

        $filter = new PasteFilter($pasteImage, 'center', 10);

        $filter->apply($image);
    }

    /**
     * @test
     */
    public function shouldConvertTopToInteger()
    {
        $pasteImage = $this->createImageMock();
        $pasteImage
            ->expects($this->never())
            ->method('getSize')
        ;

        $image = $this->createImageMock();
        $image
            ->expects($this->once())
            ->method('paste')
            ->with($pasteImage, new Point(0, 0))
        ;
        $image
            ->expects($this->never())
            ->method('getSize')
        ;

        $filter = new PasteFilter($pasteImage, 0, 'top');

        $filter->apply($image);
    }

    /**
     * @test
     */
    public function shouldConvertBottomToInteger()
    {
        $pasteImage = $this->createImageMock();
        $pasteImage
            ->expects($this->once())
            ->method('getSize')
            ->will($this->returnValue(new Box(10, 10)))
        ;

        $image = $this->createImageMock();
        $image
            ->expects($this->once())
            ->method('paste')
            ->with($pasteImage, new Point(0, 100 - 10))
        ;
        $image
            ->expects($this->once())
            ->method('getSize')
            ->will($this->returnValue(new Box(100, 100)))
        ;

        $filter = new PasteFilter($pasteImage, 0, 'bottom');

        $filter->apply($image);
    }

    /**
     * @test
     */
    public function shouldConvertMiddleToInteger()
    {
        $pasteImage = $this->createImageMock();
        $pasteImage
            ->expects($this->once())
            ->method('getSize')
            ->will($this->returnValue(new Box(10, 10)))
        ;

        $image = $this->createImageMock();
        $image
            ->expects($this->once())
            ->method('paste')
            ->with($pasteImage, new Point(0, (100 / 2) - (10 / 2)))
        ;
        $image
            ->expects($this->once())
            ->method('getSize')
            ->will($this->returnValue(new Box(100, 100)))
        ;

        $filter = new PasteFilter($pasteImage, 0, 'middle');

        $filter->apply($image);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Imagine\Image\ImageInterface
     */
    protected function createImageMock()
    {
        return $this->getMock('Imagine\\Image\\ImageInterface');
    }
}