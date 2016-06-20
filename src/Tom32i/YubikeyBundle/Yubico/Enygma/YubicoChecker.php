<?php

namespace Tom32i\YubikeyBundle\Yubico\Enygma;

use InvalidArgumentException;
use Tom32i\YubikeyBundle\Yubico\AbstractAuthYubicoChecker;
use Yubikey\Validate as Client;

/**
 * Enygma Yubico Access Checker
 * https://github.com/enygma/yubikey
 */
class YubicoChecker extends AbstractAuthYubicoChecker
{
    /**
     * {@inheritdoc}
     */
    public function isValid($oneTimePassword)
    {
        try {
            $response = $this->getClient()->check($oneTimePassword);
        } catch (InvalidArgumentException $exception) {
            return false;
        }

        return $response->success() === true;
    }

    /**
     * Get client
     *
     * @return \Yubikey\Validate
     */
    protected function getClient()
    {
        return new Client($this->apiKey, $this->clientId);
    }
}
