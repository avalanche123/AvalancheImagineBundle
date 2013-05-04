<?php

namespace Avalanche\Bundle\ImagineBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;

class AsseticCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('assetic.filter_manager')) {
            return;
        }

        $filters = $container->getParameter('imagine.filters');
        $asseticFilterManagerDef = $container->getDefinition('assetic.filter_manager');
        foreach($filters as $name => $options) {
            if (isset($options['options']) && isset($options['options']['assetic']) && ((bool) $options['options']['assetic'] === true )) {
                $filterName = 'imagine_' . $name;
                $filterClass = new DefinitionDecorator('imagine.assetic.filter');
                $filterClass->replaceArgument(2, $name);
                $container->setDefinition('imagine.assetic.filter.' . $filterName, $filterClass);
                $asseticFilterManagerDef->addMethodCall('set', array($filterName, new Reference('imagine.assetic.filter.' . $filterName)));
            }
        }
    }
}
