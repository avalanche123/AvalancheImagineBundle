<?php
namespace Avalanche\Bundle\ImagineBundle\Assetic\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Asset\FileAsset;
use Assetic\AssetManager;
use Assetic\AssetWriter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Assetic\Factory\AssetFactory;
use Assetic\Factory\Resource\FileResource;
use Symfony\Component\Routing\RouterInterface;
use Imagine\Image\ImagineInterface;
use Avalanche\Bundle\ImagineBundle\Imagine\Filter\FilterManager;
use Assetic\Filter\FilterInterface;

/**
 * The Filter itself
 */
class ImagineFilter implements FilterInterface
{
    /**
     * @var ImagineInterface
     */
    private $imagine;

    /**
     * @var FilterManager
     */
    private $filterManager;

    /**
     * @var string
     */
    private $imagineFilter;

    /**
     * Constructor.
     *
     * @param ImagineInterface $imagine       Imagine
     * @param FilterManager    $filterManager Imagine Filter Manager
     *
     * @return ImagineFilter
     */
    public function __construct(ImagineInterface $imagine, FilterManager $filterManager, $imagineFilter)
    {
        $this->imagine = $imagine;
        $this->filterManager = $filterManager;
        $this->imagineFilter = $imagineFilter;
    }

    /**
     * Not in use
     *
     * @param AssetInterface $asset The asset
     *
     * @return void
     */
    public function filterLoad(AssetInterface $asset)
    {
    }

    /**
     * Main logic is located here.
     * We parse the css here and create for all matching images a seperate asset
     *
     * @param AssetInterface $asset The asset
     *
     * @return void
     */
    public function filterDump(AssetInterface $asset)
    {
        ob_start();
        try {
            $format  = $this->filterManager->getOption($this->imagineFilter, "format", "png");

            $this->filterManager->getFilter($this->imagineFilter)
                ->apply($this->imagine->load($asset->getContent()))
                ->show($format);

            $asset->setContent(ob_get_clean());
        } catch (\Exception $e) {
            @ob_end_clean();
            throw $e;
        }
    }
}
