<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to version 3 of the GPL license,
 * that is bundled with this package in the file LICENSE, and is
 * available online at http://www.gnu.org/licenses/gpl.txt
 *
 * PHP version 7
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

declare(strict_types=1);

namespace TeamPass\Core\Domain\Dto;

use Neos\Flow\Annotations as Flow;

/**
 * Class Encrypted
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */
class Encrypted
{
    /**
     * @var string
     * @Flow\Validate(type="NotEmpty", validationGroups={"EncryptionControllerSetUserKeyPairAction"})
     */
    protected $publicKey;

    /**
     * @var string
     * @Flow\Validate(type="NotEmpty", validationGroups={"EncryptionControllerSetUserKeyPairAction"})
     * @Flow\Validate(type="NotEmpty", validationGroups={"EncryptionControllerUpdateUserPrivateKeyAction"})
     */
    protected $privateKey;

    /**
     * @var string
     */
    protected $handshakeToken;

    /**
     * @return string
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    /**
     * @param string $publicKey
     */
    public function setPublicKey(string $publicKey): void
    {
        $this->publicKey = $publicKey;
    }

    /**
     * @return string
     */
    public function getPrivateKey(): string
    {
        return $this->privateKey;
    }

    /**
     * @param string $privateKey
     */
    public function setPrivateKey(string $privateKey): void
    {
        $this->privateKey = $privateKey;
    }

    /**
     * @return string
     */
    public function getHandshakeToken(): string
    {
        return $this->handshakeToken;
    }

    /**
     * @param string $handshakeToken
     */
    public function setHandshakeToken(string $handshakeToken): void
    {
        $this->handshakeToken = $handshakeToken;
    }
}
