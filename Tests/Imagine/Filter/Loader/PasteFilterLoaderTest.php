<?php
namespace Avalanche\Bundle\ImagineBundle\Tests\Imagine\Filter\Loader;

use Avalanche\Bundle\ImagineBundle\Imagine\Filter\Loader\PasteFilterLoader;
use Makasim\File\TempFile;

class PasteFilterLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldBeConstructedWithRightArguments()
    {
        new PasteFilterLoader($this->createImagineMock());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Option "image" is required.
     */
    public function shouldThrowIfPasteImageNotSet()
    {
        $loader = new PasteFilterLoader($this->createImagineMock());

        $loader->load();
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Expected image file exists and readable.
     */
    public function shouldThrowIfPasteImageNotExists()
    {
        $loader = new PasteFilterLoader($this->createImagineMock());

        $loader->load(array(
            'image' => 'invalid/path'
        ));
    }

    /**
     * @test
     */
    public function shouldReturnPasteFilter()
    {
        $imagine = $this->createImagineMock();
        $imagine
            ->expects($this->once())
            ->method('open')
            ->will($this->returnValue($this->createImageMock()))
        ;

        $loader = new PasteFilterLoader($imagine);

        $result = $loader->load(array(
            'image' => TempFile::generate(),
        ));

        $this->assertInstanceOf('Avalanche\\Bundle\\ImagineBundle\\Imagine\\Filter\\PasteFilter', $result);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Imagine\Image\ImagineInterface
     */
    protected function createImagineMock()
    {
        return $this->getMock('Imagine\\Image\\ImagineInterface');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Imagine\Image\ImageInterface
     */
    protected function createImageMock()
    {
        return $this->getMock('Imagine\\Image\\ImageInterface');
    }
}