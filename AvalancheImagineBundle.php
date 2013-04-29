<?php

namespace Avalanche\Bundle\ImagineBundle;

use Avalanche\Bundle\ImagineBundle\DependencyInjection\Compiler\LoadersCompilerPass;
use Avalanche\Bundle\ImagineBundle\DependencyInjection\Compiler\AsseticCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AvalancheImagineBundle extends Bundle
{
    /**
     * @see Symfony\Component\HttpKernel\Bundle.Bundle::build()
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new LoadersCompilerPass());
        $container->addCompilerPass(new AsseticCompilerPass());
    }
}
