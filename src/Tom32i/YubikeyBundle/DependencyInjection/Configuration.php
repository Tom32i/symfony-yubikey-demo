<?php

namespace Tom32i\YubikeyBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('tom32i_yubikey');

        $rootNode
            ->children()
                ->arrayNode('yubico')
                    ->isRequired()
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('checker')
                            ->defaultNull()
                            ->info('Yubico checker service that implements YubicoCheckerInterface')
                        ->end()
                        ->scalarNode('client_id')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->info('Yubico client id')
                        ->end()
                        ->scalarNode('api_key')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->info('Yubico API key')
                        ->end()
                        ->arrayNode('hosts')
                            ->info('Yubico hosts list')
                            ->prototype('scalar')->end()
                        ->end()
                        ->booleanNode('https')
                            ->info('Use secure connexion')
                            ->defaultTrue()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
