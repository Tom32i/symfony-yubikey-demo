<?php

namespace Tom32i\YubikeyBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tom32i\YubikeyBundle\DependencyInjection\Compiler\YubicoCheckerCompilerPass;
use Tom32i\YubikeyBundle\Security\Factory\OTPFormLoginFactory;
use Tom32i\YubikeyBundle\DependencyInjection\UserProvider\InMemoryFactory;

class Tom32iYubikeyBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new YubicoCheckerCompilerPass());

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new OTPFormLoginFactory());
        $extension->addUserProviderFactory(new InMemoryFactory());
    }
}
