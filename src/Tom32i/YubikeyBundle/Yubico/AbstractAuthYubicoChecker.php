<?php

namespace Tom32i\YubikeyBundle\Yubico;

/**
 * Abstract Yubico Access Checker
 */
abstract class AbstractAuthYubicoChecker implements YubicoCheckerInterface
{
    /**
     * Api key
     *
     * @var string
     */
    protected $apiKey;

    /**
     * Client ID
     *
     * @var string
     */
    protected $clientId;

    /**
     * Hosts list
     *
     * @var array
     */
    protected $hosts;

    /**
     * Use secure connexion
     *
     * @var boolean
     */
    protected $https;

    /**
     * {@inheritdoc}
     */
    public function __construct($apiKey, $clientId, array $hosts = [], $https = true)
    {
        $this->apiKey = $apiKey;
        $this->clientId = $clientId;
        $this->hosts = $hosts;
        $this->https = $https;
    }

    /**
     * Get identity from one time password
     *
     * @param string $oneTimePassword
     *
     * @return string
     */
    public function getIdentity($oneTimePassword)
    {
        return substr($oneTimePassword, 0, strlen($oneTimePassword) - 32);
    }
}
