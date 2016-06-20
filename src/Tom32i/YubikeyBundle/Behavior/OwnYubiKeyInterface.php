<?php

namespace Tom32i\YubikeyBundle\Behavior;

/**
 * Owns a Yubikey
 */
interface OwnYubiKeyInterface
{
    /**
     * Get Yubikey identifier
     *
     * @return string
     */
    public function getYubikey();
}
