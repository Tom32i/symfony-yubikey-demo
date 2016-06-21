<?php

namespace Tom32i\YubikeyBundle\Security\Authenticator;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\SimpleFormAuthenticatorInterface;
use Symfony\Component\Security\Http\ParameterBagUtils;
use Tom32i\YubikeyBundle\Behavior\OwnYubiKeyInterface;
use Tom32i\YubikeyBundle\Security\Authentication\Token\UsernamePasswordOTPToken;
use Tom32i\YubikeyBundle\Yubico\YubicoCheckerInterface;

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
     * One-time-password parameter
     *
     * @var string
     */
    private $otpParameter = 'login[otp]';

    /**
     * POST method only
     *
     * @var string
     */
    private $postOnly = true;

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

        // Check that the provided one-time-password is valid.
        if (!$this->yubico->isValid($oneTimePassword)) {
            throw new CustomUserMessageAuthenticationException('Invalid OTP.');
        }

        // Check that the provided one-time-password belongs to the user.
        if ($this->getYubikey($user) !== $this->yubico->getIdentity($oneTimePassword)) {
            throw new CustomUserMessageAuthenticationException('Yubico identities mismatch.');
        }

        // Everything's in order, move along.
        return new UsernamePasswordOTPToken(
            $user,
            $user->getPassword(),
            $oneTimePassword,
            $providerKey,
            $user->getRoles()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof UsernamePasswordOTPToken && $token->getProviderKey() === $providerKey;
    }

    /**
     * {@inheritdoc}
     */
    public function createToken(Request $request, $username, $password, $providerKey)
    {
        if (!$oneTimePassword = $this->getOneTimePassword($request)) {
            throw new CustomUserMessageAuthenticationException('OTP is required');
        }

        return new UsernamePasswordOTPToken($username, $password, $oneTimePassword, $providerKey);
    }

    /**
     * Get the One-time-password from the Request
     *
     * @param Request $request
     *
     * @return string
     */
    private function getOneTimePassword(Request $request)
    {
        if ($this->postOnly) {
            return ParameterBagUtils::getParameterBagValue($request->request, $this->otpParameter);
        } else {
            return ParameterBagUtils::getRequestParameterValue($request, $this->otpParameter);
        }
    }

    /**
     * Get yubikey identifier for the given user
     *
     * @param UserInterface $user
     *
     * @return string
     */
    private function getYubikey(UserInterface $user)
    {
        if ($user instanceof OwnYubiKeyInterface) {
            return $user->getYubiKey();
        }

        throw new CustomUserMessageAuthenticationException('User is not associated to a Yubikey');
    }
}
