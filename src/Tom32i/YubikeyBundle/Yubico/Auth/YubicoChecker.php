<?php

namespace Tom32i\YubikeyBundle\Yubico\Auth;

use Tom32i\YubikeyBundle\Yubico\AbstractAuthYubicoChecker;
use Auth_Yubico as Client;

/**
 * Auth_Yubico Yubico Access Checker
 * https://github.com/Yubico/php-yubico
 */
class YubicoChecker extends AbstractAuthYubicoChecker
{
    /**
     * {@inheritdoc}
     */
    public function isValid($oneTimePassword)
    {
        $result = $this->getClient()->verify($oneTimePassword);

        return \PEAR::isError($result);
    }

    /**
     * Get client
     *
     * @return \Auth_Yubico
     */
    protected function getClient()
    {
        $client = new Client($this->clientId, $this->apiKey);

        foreach ($this->hosts as $host) {
            $client->addURLpart($host);
        }

        return $client;
    }
}
