<?php

namespace Tom32i\YubikeyBundle\Security\Authenticator;

use Tom32i\YubikeyBundle\Security\Authentication\Token\UsernamePasswordOTPToken;
use Tom32i\YubikeyBundle\Yubico\YubicoCheckerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\SimpleFormAuthenticatorInterface;

/**
 * One-time-password Simple Form Authenticator
 */
class OneTimePasswordFormAuthenticator implements SimpleFormAuthenticatorInterface
{
    /**
     * User Password encoder
     *
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * Yubico OTP Checker
     *
     * @var YubicoCheckerInterface
     */
    private $yubico;

    /**
     * Constructor
     *
     * @param UserPasswordEncoderInterface $encoder
     * @param YubicoCheckerInterface $yubico
     */
    public function __construct(UserPasswordEncoderInterface $encoder, YubicoCheckerInterface $yubico)
    {
        $this->encoder = $encoder;
        $this->yubico = $yubico;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        // Check that the user exists.
        try {
            $user = $userProvider->loadUserByUsername($token->getUsername());
        } catch (UsernameNotFoundException $e) {
            throw new CustomUserMessageAuthenticationException('Invalid username or password');
        }

        // Check that the provided password is valid.
        if (!$this->encoder->isPasswordValid($user, $token->getCredentials())) {
            throw new CustomUserMessageAuthenticationException('Invalid username or password');
        }

        $oneTimePassword = $token->getOneTimePassword();

        // Check that the provided one-time-password belongs to the user.
        /*if ($user->getYubiKey() != $this->yubico->getIdentity($oneTimePassword)) {
            throw new CustomUserMessageAuthenticationException('Yubico identities mismatch.');
        }*/

        // Check that the provided one-time-password is valid.
        if (!$this->yubico->isValid($oneTimePassword)) {
            throw new CustomUserMessageAuthenticationException('Invalid OTP.');
        }

        $token = new UsernamePasswordOTPToken(
            $user,
            $user->getPassword(),
            $oneTimePassword,
            $providerKey,
            $user->getRoles()
        );

        // Everything's in order, move along.
        return $token;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof UsernamePasswordOTPToken
            && $token->getProviderKey() === $providerKey;
    }

    /**
     * {@inheritdoc}
     */
    public function createToken(Request $request, $username, $password, $providerKey)
    {
        $oneTimePassword = $request->request->get('_otp', null);

        return new UsernamePasswordOTPToken($username, $password, $oneTimePassword, $providerKey);
    }
}
