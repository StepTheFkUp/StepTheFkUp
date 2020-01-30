<?php
declare(strict_types=1);

namespace EonX\EasySecurity\Bridge\Symfony\DependencyInjection;

use \Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('easy_security');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('token_decoder')->defaultValue('chain')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
