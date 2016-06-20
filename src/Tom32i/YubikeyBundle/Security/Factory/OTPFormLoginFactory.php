<?php

namespace Tom32i\YubikeyBundle\Security\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\FormLoginFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

class OTPFormLoginFactory extends FormLoginFactory
{
    /**
     * {@inhertidoc}
     */
    public function __construct()
    {
        parent::__construct();

        $this->addOption('otp_parameter', 'login[otp]');
        $this->addOption('username_parameter', 'login[username]');
        $this->addOption('password_parameter', 'login[password]');
    }

    /**
     * {@inhertidoc}
     */
    public function getKey()
    {
        return 'otp-form-login';
    }

    /**
     * {@inhertidoc}
     */
    protected function getListenerId()
    {
        return 'security.authentication.listener.otp_form';
    }

    /**
     * {@inhertidoc}
     */
    protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
    {
        $provider = 'security.authentication.provider.otp_dao.'.$id;
        $container
            ->setDefinition($provider, new DefinitionDecorator('security.authentication.provider.otp_dao'))
            ->replaceArgument(0, new Reference($userProviderId))
            ->replaceArgument(1, new Reference('security.user_checker.'.$id))
            ->replaceArgument(2, $id)
        ;

        return $provider;
    }

    /**
     * {@inhertidoc}
     */
    protected function createEntryPoint($container, $id, $config, $defaultEntryPoint)
    {
        $entryPointId = 'security.authentication.otp_form_entry_point.'.$id;
        $container
            ->setDefinition($entryPointId, new DefinitionDecorator('security.authentication.form_entry_point'))
            ->addArgument(new Reference('security.http_utils'))
            ->addArgument($config['login_path'])
            ->addArgument($config['use_forward'])
        ;

        return $entryPointId;
    }
}
