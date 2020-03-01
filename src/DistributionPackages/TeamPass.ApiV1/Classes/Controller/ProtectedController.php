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

namespace TeamPass\ApiV1\Controller;

use GibberishAES\GibberishAES;
use Neos\Flow\Session\Exception\SessionNotStartedException;
use TeamPass\Core\Exception\InvalidSessionHttpException;

/**
 * Class ProtectedController
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */
abstract class ProtectedController extends AbstractController
{

    /**
     * @throws \Neos\Flow\Session\Exception\SessionNotStartedException
     * @throws \Exception
     */
    protected function initializeAction(): void
    {
        parent::initializeAction();

        // check for a valid session. checking only the session id is not enough!
        if (!$this->session->isStarted() || !$this->session->getData('identifier')) {
            throw new InvalidSessionHttpException(
                "Can't find a valid session. Please re-login."
            );
        }
        $this->checkCSRFToken();
    }

    /**
     * validates the csrf token in request header
     *
     * @return void
     * @throws \Exception
     */
    protected function checkCSRFToken(): void
    {
        try {
            $aesKey = $this->session->getData("identifier");

            $token = $this->request->getHttpRequest()->getHeader("X-Csrf-Token");
            $token = current($token);

            if (!$token) {
                throw new \Exception("could not read csrf token from request");
            }

            $base64Token = base64_decode($token);

            if (!$base64Token) {
                throw new \Exception("error while decoding csrf token");
            }

            $this->aesDecrypt($base64Token, $aesKey);
        } catch (\Exception $e) {
            $this->logger->critical("CSRFToken Error:" . $e->getMessage());
            throw $e;
        }
    }

    /**
     * encrypts string with session aes key
     *
     * @param string $value the string to encrypt
     *
     * @return mixed
     *
     * @throws InvalidSessionHttpException
     * @throws \Exception
     */
    protected function aesEncrypt(string $value)
    {
        // get session aes key from session
        $sessionAesKey = $this->sessionAesKey();

        // encrypt given value with session aes key
        return GibberishAES::enc($value, $sessionAesKey);
    }

    /**
     * decrypts string with session aes key
     *
     * @param string $value decrypted value
     * @param string $key   optional aes key to decrypt message. if none is given, the default aes key will be used
     *
     * @return mixed
     * @throws \Exception
     */
    protected function aesDecrypt(string $value, $key = null)
    {
        if ($key === null) {
            // get session aes key from session
            $aesKey = $this->sessionAesKey();
        } else {
            $aesKey = $key;
        }

        // decrypt given value with session aes key
        $decrypted = GibberishAES::dec($value, $aesKey);

        if ($decrypted === false) {
            throw new \Exception("FATAL: aes decryption failed");
        }

        return $decrypted;
    }

    /**
     * encodes given string to json
     *
     * @param array $params value to encode
     *
     * @return string
     */
    protected function encode(array $params): string
    {
        return json_encode($params);
    }

    /**
     * decodes a json string to array
     *
     * @param string $params json string to decode
     *
     * @return array
     */
    protected function decode(string $params): array
    {
        return json_decode($params, true);
    }

    /**
     * Returns session aes key for user
     *
     * @return string
     *
     * @throws InvalidSessionHttpException
     * @throws SessionNotStartedException
     */
    protected function sessionAesKey(): string
    {
        $sessionAesKey = (string) $this->session->getData("sessionAesKey");
        if (!empty($sessionAesKey)) {
            return $sessionAesKey;
        }

        $user = $this->session->getData("userName");
        throw new InvalidSessionHttpException("Session aes key for User '$user' not found!");
    }

    /**
     * encrypts given content with given rsa public key
     *
     * @param string $content   the content to encrypt
     * @param string $publicKey the rsa public key
     *
     * @return string
     * @throws \Exception
     */
    protected function rsaEncrypt(string $content, string $publicKey): string
    {
        if (!openssl_public_encrypt($content, $output, $publicKey)) {
            throw new \Exception("rsa encryption failed");
        }
        return base64_encode($output);
    }

    /**
     * decrypts given content with given rsa private key
     *
     * @param string $encryptedContent the encrypted content
     * @param string $privateKey       the users private key
     *
     * @return mixed
     * @throws \Exception
     */
    protected function rsaDecrypt(string $encryptedContent, string $privateKey)
    {
        if (!openssl_private_decrypt(base64_decode($encryptedContent), $output, $privateKey)) {
            throw new \Exception("rsa decryption failed");
        }
        return $output;
    }
}
