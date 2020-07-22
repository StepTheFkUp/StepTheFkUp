<?php

declare(strict_types=1);

namespace EonX\EasyWebhook\Bridge\Symfony\DependencyInjection;

use EonX\EasyWebhook\Signers\Rs256Signer;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('easy_webhook');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('async')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                        ->scalarNode('bus')->defaultValue('messenger.bus.default')->end()
                    ->end()
                ->end()
                ->scalarNode('method')->defaultNull()->end()
                ->arrayNode('signature')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultFalse()->end()
                        ->scalarNode('signature_header')->defaultNull()->end()
                        ->scalarNode('signer')->defaultValue(Rs256Signer::class)->end()
                        ->scalarNode('secret')->defaultNull()->end()
                    ->end()
                ->end()
                ->booleanNode('use_default_configurators')->defaultTrue()->end()
            ->end();

        return $treeBuilder;
    }
}
