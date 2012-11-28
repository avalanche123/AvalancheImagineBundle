<?php

namespace Avalanche\Bundle\ImagineBundle\Controller;

use Avalanche\Bundle\ImagineBundle\Imagine\Filter\FilterManager;
use Imagine\Image\ImagineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Avalanche\Bundle\ImagineBundle\Imagine\CacheManager;

class ImagineController
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var ImagineInterface
     */
    private $imagine;

    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @var Avalanche\Bundle\ImagineBundle\Imagine\Filter\FilterManager
     */
    private $filterManager;

    /**
     * Constructs by setting $cachePathResolver
     *
     * @param Request          $request
     * @param ImagineInterface $imagine
     * @param CacheManager     $cacheManager
     * @param FilterManager    $filterManager
     */
    public function __construct(Request $request, ImagineInterface $imagine, CacheManager $cacheManager, FilterManager $filterManager)
    {
        $this->request = $request;
        $this->imagine = $imagine;
        $this->cacheManager = $cacheManager;
        $this->filterManager = $filterManager;
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
        $cachedPath = $this->cacheManager->cacheImage($this->request->getBaseUrl(), $path, $filter);
        
         // if cache path cannot be determined, return 404
        if (null === $cachedPath) {
            throw new NotFoundHttpException('Image doesn\'t exist');
        }

        ob_start();
        try {
            $format  = $this->filterManager->getOption($filter, "format", "png");

            $this->imagine->open($cachedPath)->show($format);

            $type    = 'image/' . $format;
            $length  = ob_get_length();
            $content = ob_get_clean();

            // TODO: add more media headers
            $response = new Response($content, 201, array(
                'content-type'   => $type,
                'content-length' => $length,
            ));

            // Cache
            $cacheType = $this->filterManager->getOption($filter, "cache_type", false);
            if (false == $cacheType) {
                return $response;
            }

            ($cacheType === "public") ? $response->setPublic() : $response->setPrivate();

            $cacheExpires = $this->filterManager->getOption($filter, "cache_expires", "1 day");
            $expirationDate = new \DateTime("+" . $cacheExpires);
            $maxAge = $expirationDate->format("U") - time();

            if ($maxAge < 0) {
                throw new \InvalidArgumentException("Invalid cache expiration date");
            }

            $response->setExpires($expirationDate);
            $response->setMaxAge($maxAge);

            return $response;
        } catch (\Exception $e) {
            ob_end_clean();
            throw $e;
        }
    }
}
