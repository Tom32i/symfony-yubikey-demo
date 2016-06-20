<?php

namespace Tom32i\YubikeyBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type;

/**
 * Login form type
 */
class LoginType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', Type\TextType::class);
        $builder->add('password', Type\PasswordType::class);

        if ($options['second_factor']) {
            $builder->add('otp', OneTimePasswordType::class);
        }

        $builder->add('remember_me', Type\CheckboxType::class, [
            'required' => false,
            'data' => true,
        ]);

        $builder->add('submit', Type\SubmitType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'method' => 'POST',
            'second_factor' => true,
            'csrf_protection' => true,
            'csrf_field_name' => 'csrf_token',
            'intention' => 'authenticate',
        ]);
    }
}
