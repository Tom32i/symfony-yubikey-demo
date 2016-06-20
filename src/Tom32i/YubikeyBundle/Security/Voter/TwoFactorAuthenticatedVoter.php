<?php

namespace Tom32i\YubikeyBundle\Security\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Tom32i\YubikeyBundle\Security\Authentication\Token\UsernamePasswordOTPToken;

class TwoFactorAuthenticatedVoter implements VoterInterface
{
    /**
     * Is Authenticated with Two-Factor authentication role
     */
    const IS_AUTHENTICATED_TWO_FACTOR = 'IS_AUTHENTICATED_TWO_FACTOR';

    /**
     * {@inheritdoc}
     */
    public function vote(TokenInterface $token, $subject, array $attributes)
    {
        $result = VoterInterface::ACCESS_ABSTAIN;

        foreach ($attributes as $attribute) {
            if (null === $attribute || self::IS_AUTHENTICATED_TWO_FACTOR !== $attribute) {
                continue;
            }

            return $this->isTwoFactor($token) ? VoterInterface::ACCESS_GRANTED : VoterInterface::ACCESS_DENIED;
        }

        return $result;
    }

    /**
     * Is the given token an UsernamePasswordOTPToken?
     */
    private function isTwoFactor(TokenInterface $token)
    {
        return $token instanceof UsernamePasswordOTPToken;
    }
}
