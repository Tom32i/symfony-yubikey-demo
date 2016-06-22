<?php

namespace Tom32i\YubikeyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Tom32i\YubikeyBundle\Form\Type\LoginType;

class SecurityController extends Controller
{
    /**
     * Login action
     *
     * @param string $template Default template to render the login page
     * @param boolean $second_factor Adds the second factor to the login form
     *
     * @return array
     */
    public function loginAction($template = 'Tom32iYubikeyBundle:Security:login.html.twig', $second_factor = true)
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        $form = $this->createForm(
            LoginType::class,
            ['username' => $authenticationUtils->getLastUsername()],
            [
                'second_factor' => $second_factor,
                'action' => $this->generateUrl('login'),
            ]
        );

        if ($exception = $authenticationUtils->getLastAuthenticationError()) {
            $form->addError($this->getFormError($exception));
        }

        return $this->render($template, ['form' => $form->createView()]);
    }

    /**
     * Get form error from authentication exception
     *
     * @param AuthenticationException $exception
     *
     * @return FormError
     */
    protected function getFormError(AuthenticationException $exception)
    {
        return new FormError(
            $exception->getMessageKey(),
            $exception->getMessageKey(),
            $exception->getMessageData(),
            null,
            $exception
        );
    }
}
