<?php

namespace Tom32i\YubikeyBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Reference;
use Tom32i\YubikeyBundle\Yubico\YubicoCheckerInterface;

/**
 * Resolve the Yubico Chercker service
 */
class YubicoCheckerCompilerPass implements CompilerPassInterface
{
    /**
     * Container
     *
     * @var ContainerBuilder
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->container = $container;

        $config = $container->getParameter('tom32i_yubikey.config.yubico');

        // Setup arguments for abstract Yubico Checker service
        $container
            ->getDefinition('tom32i_yubikey.yubico_checker')
            ->setArguments([
                $config['api_key'],
                $config['client_id'],
            ])
        ;

        // Get concrete Yubico Checker service id
        $checker = $config['checker'] ?: $this->getDefaultYubicoChecker();

        // Ensure that the checker service exists
        if (!$container->hasDefinition($checker)) {
            throw new InvalidArgumentException(sprintf(
                'Yubico checker service %s does not exists.',
                $config['checker']
            ));
        }

        $definition = $container->getDefinition($checker);

        // Ensure that the checker service implements the YubicoCheckerInterface
        if (!in_array(YubicoCheckerInterface::class, class_implements($definition->getClass()))) {
            throw new InvalidArgumentException(sprintf(
                'Yubico checker service must implement "%s".',
                YubicoCheckerInterface::class
            ));
        }

        // Add the checker to the Authentication provider
        $container
            ->getDefinition('security.authentication.provider.otp_dao')
            ->addArgument(new Reference($checker));

        // Add the checker to the Two-Factor Simple Form Authenticator
        $container
            ->getDefinition('tom32i_yubikey.simpl_form_authenticator.one_time_password')
            ->addArgument(new Reference($checker));
    }

    /**
     * Get default checker service definition
     *
     * @return Definition
     */
    private function getDefaultYubicoChecker()
    {
        if (class_exists('Auth_Yubico')) {
            return $this->createCheckerService(
                'tom32i_yubikey.yubico_checker.auth',
                'Tom32i\YubikeyBundle\Yubico\Auth\YubicoChecker'
            );
        }

        if (class_exists('Yubikey\Validate')) {
            return $this->createCheckerService(
                'tom32i_yubikey.yubico_checker.enygma',
                'Tom32i\YubikeyBundle\Yubico\Enygma\YubicoChecker'
            );
        }

        throw new InvalidArgumentException('No Yubico checker service available.');
    }

    /**
     * Create checker service
     *
     * @param string $id
     * @param string $className
     *
     * @return string The id of the service
     */
    private function createCheckerService($id, $className)
    {
        $definition = new DefinitionDecorator('tom32i_yubikey.yubico_checker');

        $definition->setClass($className);

        $this->container->setDefinition($id, $definition);

        return $id;
    }
}
