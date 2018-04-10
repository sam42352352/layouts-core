<?php

namespace Netgen\Bundle\BlockManagerBundle;

use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class NetgenBlockManagerBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new CompilerPass\Block\HandlerPluginPass());
        $container->addCompilerPass(new CompilerPass\Block\BlockDefinitionPass());
        $container->addCompilerPass(new CompilerPass\LayoutResolver\TargetTypePass());
        $container->addCompilerPass(new CompilerPass\LayoutResolver\ConditionTypePass());
        $container->addCompilerPass(new CompilerPass\LayoutResolver\DoctrineTargetHandlerPass());
        $container->addCompilerPass(new CompilerPass\LayoutResolver\Form\ConditionTypePass());
        $container->addCompilerPass(new CompilerPass\LayoutResolver\Form\TargetTypePass());
        $container->addCompilerPass(new CompilerPass\View\TemplateResolverPass());
        $container->addCompilerPass(new CompilerPass\View\ViewBuilderPass());
        $container->addCompilerPass(new CompilerPass\View\FragmentRendererPass());
        $container->addCompilerPass(new CompilerPass\View\DefaultViewTemplatesPass());
        $container->addCompilerPass(new CompilerPass\Parameters\FormMapperPass());
        $container->addCompilerPass(new CompilerPass\Parameters\ParameterFilterPass());
        $container->addCompilerPass(new CompilerPass\Parameters\ParameterTypePass());
        $container->addCompilerPass(new CompilerPass\Item\ValueTypePass());
        $container->addCompilerPass(new CompilerPass\Item\ItemLoaderPass());
        $container->addCompilerPass(new CompilerPass\Item\ItemBuilderPass());
        $container->addCompilerPass(new CompilerPass\Item\UrlGeneratorPass());
        $container->addCompilerPass(new CompilerPass\Collection\ItemDefinitionPass());
        $container->addCompilerPass(new CompilerPass\Collection\QueryTypePass());
        $container->addCompilerPass(new CompilerPass\Layout\LayoutTypePass());
        $container->addCompilerPass(new CompilerPass\Block\BlockTypePass());
        $container->addCompilerPass(new CompilerPass\Block\BlockTypeGroupPass());
        $container->addCompilerPass(new CompilerPass\HttpCache\CacheManagerPass());
        $container->addCompilerPass(new CompilerPass\HttpCache\ConfigureHttpCachePass());
        $container->addCompilerPass(new CompilerPass\HttpCache\Block\CacheableResolverPass());
        $container->addCompilerPass(new CompilerPass\Context\ContextBuilderPass());
        $container->addCompilerPass(new CompilerPass\Transfer\SerializationVisitorPass());
        $container->addCompilerPass(new CompilerPass\Twig\RuntimeLoaderPass());
        $container->addCompilerPass(new CompilerPass\Design\ThemePass());
    }
}
