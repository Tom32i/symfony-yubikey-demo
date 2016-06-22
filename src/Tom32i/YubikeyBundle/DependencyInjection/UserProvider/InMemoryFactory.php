<?php

namespace Tom32i\YubikeyBundle\DependencyInjection\UserProvider;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\UserProvider\UserProviderFactoryInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

/**
 * InMemoryFactory creates services for the memory provider.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
class InMemoryFactory implements UserProviderFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(ContainerBuilder $container, $id, $config)
    {
        $definition = $container->setDefinition($id, new DefinitionDecorator('tom32i_yubikey.security.user.provider.in_memory'));

        foreach ($config['users'] as $username => $user) {
            $userId = $id.'_'.$username;

            $container
                ->setDefinition($userId, new DefinitionDecorator('tom32i_yubikey.security.user.provider.in_memory.user'))
                ->setArguments(array($username, (string) $user['password'], (string) $user['yubikey'], $user['roles']))
            ;

            $definition->addMethodCall('createUser', array(new Reference($userId)));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return 'memory_yubikey';
    }

    /**
     * {@inheritdoc}
     */
    public function addConfiguration(NodeDefinition $node)
    {
        $node
            ->fixXmlConfig('user')
            ->children()
                ->arrayNode('users')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('password')->defaultValue(uniqid('', true))->end()
                            ->arrayNode('roles')
                                ->beforeNormalization()->ifString()->then(function ($v) { return preg_split('/\s*,\s*/', $v); })->end()
                                ->prototype('scalar')->end()
                            ->end()
                            ->scalarNode('yubikey')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
