<?php

namespace Tom32i\YubikeyBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Tom32i\YubikeyBundle\Behavior\OwnYubiKeyInterface;
use Tom32i\YubikeyBundle\Security\Authentication\Token\UsernamePasswordOTPToken;
use Tom32i\YubikeyBundle\Yubico\YubicoCheckerInterface;

class OtpDaoAuthenticationProvider extends DaoAuthenticationProvider
{
    /**
     * Yubico one-time-password checker
     *
     * @var YubicoCheckerInterface
     */
    private $yubicoChecker;

    /**
     * Constructor.
     *
     * @param UserProviderInterface   $userProvider               An UserProviderInterface instance
     * @param UserCheckerInterface    $userChecker                An UserCheckerInterface instance
     * @param string                  $providerKey                The provider key
     * @param EncoderFactoryInterface $encoderFactory             An EncoderFactoryInterface instance
     * @param bool                    $hideUserNotFoundExceptions Whether to hide user not found exception or not
     */
    public function __construct(
        UserProviderInterface $userProvider,
        UserCheckerInterface $userChecker,
        $providerKey,
        EncoderFactoryInterface $encoderFactory,
        $hideUserNotFoundExceptions,
        YubicoCheckerInterface $yubicoChecker
    ) {
        parent::__construct($userProvider, $userChecker, $providerKey, $encoderFactory, $hideUserNotFoundExceptions);

        $this->yubicoChecker = $yubicoChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(TokenInterface $token)
    {
        $authenticatedToken = parent::authenticate($token);

        if (!$token instanceof UsernamePasswordOTPToken) {
            return $authenticatedToken;
        }

        return new UsernamePasswordOTPToken(
            $authenticatedToken->getUser(),
            $authenticatedToken->getCredentials(),
            $token->getOneTimePassword(),
            $authenticatedToken->getProviderKey(),
            $authenticatedToken->getRoles()
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function checkAuthentication(UserInterface $user, UsernamePasswordToken $token)
    {
        parent::checkAuthentication($user, $token);

        if (!$token instanceof UsernamePasswordOTPToken) {
            return;
        }

        $oneTimePassword = $token->getOneTimePassword();

        // Check that the provided one-time-password is valid.
        if (!$this->yubicoChecker->isValid($oneTimePassword)) {
            throw new BadCredentialsException('Invalid OTP.');
        }

        // Check that the provided one-time-password belongs to the user.
        if ($this->getYubikey($user) !== $this->yubicoChecker->getIdentity($oneTimePassword)) {
            throw new BadCredentialsException('Yubico identities mismatch.');
        }
    }

    /**
     * Get yubikey identifier for the given user
     *
     * @param UserInterface $user
     *
     * @return string
     */
    protected function getYubikey(UserInterface $user)
    {
        if ($user instanceof OwnYubiKeyInterface) {
            return $user->getYubiKey();
        }

        throw new BadCredentialsException('User is not associated to a Yubikey');
    }
}
