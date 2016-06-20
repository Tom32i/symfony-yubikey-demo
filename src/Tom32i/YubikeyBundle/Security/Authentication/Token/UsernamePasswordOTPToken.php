<?php

namespace Tom32i\YubikeyBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Username + Password + One-Time-Password Token
 */
class UsernamePasswordOTPToken extends UsernamePasswordToken
{
    /**
     * One time password
     *
     * @var string
     */
    private $oneTimePassword;

    /**
     * {@inheritdoc}
     */
    public function __construct($user, $credentials, $oneTimePassword, $providerKey, array $roles = array())
    {
        parent::__construct($user, $credentials, $providerKey, $roles);

        $this->oneTimePassword = $oneTimePassword;
    }

    /**
     * Get one-time-password
     *
     * @return string
     */
    public function getOneTimePassword()
    {
        return $this->oneTimePassword;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
        parent::eraseCredentials();

        $this->oneTimePassword = null;
    }
}
