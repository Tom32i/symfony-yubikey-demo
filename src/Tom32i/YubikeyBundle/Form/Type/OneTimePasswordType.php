<?php

namespace Tom32i\YubikeyBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

/**
 * One-time-password form type
 */
class OneTimePasswordType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['attr']['autocomplete'] = 'off';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return PasswordType::class;
    }
}
