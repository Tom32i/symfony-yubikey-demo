<?php

namespace Tom32i\YubikeyBundle\Yubico;

/**
 * Yubico Access Checker
 */
interface YubicoCheckerInterface
{
    /**
     * Will be called automatically to provide the service with Yubico parameters.
     *
     * @param string $apiKey
     * @param string $clientId
     */
    public function __construct($apiKey, $clientId);

    /**
     * Is OTP valid
     *
     * @param string $oneTimePassword
     *
     * @return boolean
     */
    public function isValid($oneTimePassword);

    /**
     * Get identity from one time password
     *
     * @param string $oneTimePassword
     *
     * @return string
     */
    public function getIdentity($oneTimePassword);
}
