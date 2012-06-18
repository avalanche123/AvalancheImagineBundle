<?php

namespace Avalanche\Bundle\ImagineBundle\Controller;

use Avalanche\Bundle\ImagineBundle\Imagine\CachePathResolver;
use Avalanche\Bundle\ImagineBundle\Imagine\Filter\FilterManager;
use Imagine\Image\ImagineInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ImagineController
{
    /**
     * @var Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     * @var Avalanche\Bundle\ImagineBundle\Imagine\CachePathResolver
     */
    private $cachePathResolver;

    /**
     * @var Imagine\Image\ImagineInterface
     */
    private $imagine;

    /**
     * @var Avalanche\Bundle\ImagineBundle\Imagine\FilterManager
     */
    private $filterManager;

    /**
     * @var Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $webRoot;

    /**
     * Constructs by setting $cachePathResolver
     *
     * @param Symfony\Component\HttpFoundation\Request                 $request
     * @param Avalanche\Bundle\ImagineBundle\Imagine\CachePathResolver $cachePathResolver
     * @param Imagine\Image\ImagineInterface                           $imagine
     * @param Avalanche\Bundle\ImagineBundle\Imagine\FilterManager     $filterManager
     * @param Symfony\Component\Filesystem\Filesystem                  $filesystem
     * @param string                                                   $webRoot
     */
    public function __construct(
        Request $request,
        CachePathResolver $cachePathResolver,
        ImagineInterface $imagine,
        FilterManager $filterManager,
        Filesystem $filesystem,
        $webRoot
    )
    {
        $this->request           = $request;
        $this->cachePathResolver = $cachePathResolver;
        $this->imagine           = $imagine;
        $this->filterManager     = $filterManager;
        $this->filesystem        = $filesystem;
        $this->webRoot           = $webRoot;
    }

    /**
     * This action applies a given filter to a given image, saves the image and
     * outputs it to the browser at the same time
     *
     * @param string $path
     * @param string $filter
     *
     * @return Response
     */
    public function filter($path, $filter)
    {
        $path = '/'.ltrim($path, '/');

        //TODO: find out why I need double urldecode to get a valid path
        $browserPath = urldecode(urldecode($this->cachePathResolver->getBrowserPath($path, $filter)));
        $basePath = $this->request->getBaseUrl();

        if (!empty($basePath) && 0 === strpos($browserPath, $basePath)) {
             $browserPath = substr($browserPath, strlen($basePath));
        }

         // if cache path cannot be determined, return 404
        if (null === $browserPath) {
            throw new NotFoundHttpException('Image doesn\'t exist');
        }

        $realPath = $this->webRoot.$browserPath;
        $sourcePath = $this->webRoot.$path;

        if (!file_exists($sourcePath)) {
            throw new NotFoundHttpException(sprintf(
                'Source image not found in "%s"', $sourcePath
            ));
        }

        $ext = pathinfo($sourcePath, PATHINFO_EXTENSION);
        if($ext == 'gif' && $this->isAnimatedGif(fopen($sourcePath, 'r')))
        {
            ob_start();
            echo stream_get_contents(fopen($sourcePath, 'r'));
            return new Response(ob_get_clean(), 201, array(
                'content-type' => 'image/gif',
            ));
        }

        // if the file has already been cached, we're probably not rewriting
        // correctly, hence make a 301 to proper location, so browser remembers
        if (file_exists($realPath)) {
            return new Response('', 301, array(
                'location' => $this->request->getBasePath().$browserPath
            ));
        }

        $dir = pathinfo($realPath, PATHINFO_DIRNAME);

        if (!is_dir($dir)) {
            if (!$this->filesystem->mkdir($dir)) {
                throw new \RuntimeException(sprintf(
                    'Could not create directory %s', $dir
                ));
            }
        }

        ob_start();
        try {
            // TODO: get rid of hard-coded quality and format
            $this->filterManager->get($filter)
                ->apply($this->imagine->open($sourcePath))
                ->save($realPath, array('quality' => 100))
                ->show('png');

            // TODO: add more media headers
            return new Response(ob_get_clean(), 201, array(
                'content-type' => 'image/png',
            ));
        } catch (\Exception $e) {
            ob_end_clean();
            throw $e;
        }
    }

    /**
     * Detects whether the given GIF image data contains more than one frame
     *
     * @param resource $image stream resource containing the binary GIF data
     * @see http://www.php.net/manual/en/function.imagecreatefromgif.php#88005
     * @see http://en.wikipedia.org/wiki/Graphics_Interchange_Format#Animated_GIF
     *
     * @return boolean true if gif contains more than one frame
     */
    protected function isAnimatedGif($image)
    {
        //an animated gif contains multiple "frames", with each frame having a
        //header made up of:
        // * a static 4-byte sequence (\x00\x21\xF9\x04)
        // * 4 variable bytes
        // * a static 2-byte sequence (\x00\x2C) (some variants may use \x00\x21 ?)
        $count = preg_match_all('#\x00\x21\xF9\x04.{4}\x00(\x2C|\x21)#s', stream_get_contents($image), $m);

        // rewind the stream file pointer to allow further reads from the stream
        rewind($image);
        return $count >= 2;
    }
}
