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

namespace TeamPass\Core\Property\TypeConverter;

use Neos\Flow\Annotations as Flow;
use GibberishAES\GibberishAES;
use Neos\Flow\Property\TypeConverter\ObjectConverter;
use Neos\Flow\Session\SessionInterface;

/**
 * Class TeamPassDtoConverter
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 *
 * @Flow\Scope("singleton")
 */

class TeamPassDtoConverter extends ObjectConverter
{
    /**
     * @var string
     */
    protected const RSA_PUB_KEY_BEGIN_STRING = "-----BEGIN PUBLIC KEY-----";

    /**
     * @var string
     */
    protected const NEW_LINE = "\r\n";

    /**
     * @var string
     */
    protected const RSA_PUB_KEY_END_STRING = "-----END PUBLIC KEY-----";

    /**
     * @var int
     */
    protected $priority = 10;

    /**
     * @Flow\Inject
     * @var SessionInterface
     */
    protected $session;

    /**
     * Convert all properties in the source array
     *
     * @param mixed $source
     * @return array
     *
     * @throws \Exception
     */
    public function getSourceChildPropertiesToBeConverted($source)
    {
        $source = $this->decryptSource($source);

        if (isset($source["publicKey"])) {
            $source["publicKey"] = $this->normalizePublicKey($source["publicKey"]);
        }

        return $source;
    }

    /**
     * decrypts the 'encryptedData' value of given source array into new source values
     *
     * @param array $source

     * @return array
     * @throws \Exception
     */
    protected function decryptSource(array $source): array
    {
        $decryptedData = [];
        if (isset($source["encryptedData"])) {
            $aesKey = $this->getAesKeyFromSession();
            $decryptedJsonData = $this->aesDecrypt($source["encryptedData"], $aesKey);
            $decryptedData = json_decode($decryptedJsonData, true);
            unset($source["encryptedData"]);
            unset($decryptedData["encryptedData"]);
        }

        $source = array_merge($source, $decryptedData);

        if (isset($source["encryptedContent"])) {
            $aesKey = $this->getAesKeyFromSession();
            $source["decryptedContent"] = $this->aesDecrypt($source["encryptedContent"], $aesKey);
            unset($source["encryptedContent"]);
        }

        if (isset($source["encAesKey"])) {
            if ($source["encAesKey"] === false) {
                $source["aesKey"] = $source["encAesKey"];
                unset($source["encAesKey"]);
            } else {
                $aesKey = $this->getAesKeyFromSession();
                $source["aesKey"] = $this->aesDecrypt($source["encAesKey"], $aesKey);
                unset($source["encAesKey"]);
            }
        }

        return $source;
    }

    /**
     * @return string
     * @throws \Neos\Flow\Session\Exception\SessionNotStartedException
     * @throws \Exception
     */
    protected function getAesKeyFromSession(): string
    {
        if (!$this->session->isStarted() || empty($sessionAesKey = (string) $this->session->getData("sessionAesKey"))) {
            throw new \Exception("mae");
        }
        return (string) $this->session->getData("sessionAesKey");
    }

    /**
     * decrypts string with session aes key
     *
     * @param string $value decrypted value
     * @param string $key   aes key to decrypt message.
     *
     * @return string
     * @throws \Exception
     */
    protected function aesDecrypt(string $value, string $key): string
    {
        $decrypted = GibberishAES::dec($value, $key);

        if ($decrypted === false) {
            throw new \Exception("FATAL: aes decryption failed");
        }

        return $decrypted;
    }

    /**
     * format public key to be readable by openssl
     *
     * @param string $rawPublicKey the rsa unformed rsa public key
     *
     * @return string
     */
    protected function normalizePublicKey(string $rawPublicKey): string
    {
        // sometimes the key comes with a newline at the end, but sometimes not. We delete all new lines
        // an add it to string
        $rawPublicKey = trim($rawPublicKey);

        // format the string as a openssl-like key (with line breaks), also adds a newline at the end!
        $publicKey = chunk_split($rawPublicKey);

        // check if key has already a rsa header
        if (strpos($publicKey, self::RSA_PUB_KEY_BEGIN_STRING) === false) {
            // add rsa header to key string
            $publicKey = self::RSA_PUB_KEY_BEGIN_STRING . self::NEW_LINE .  $publicKey;
        }

        // check if key has already a rsa footer
        if (strpos($publicKey, self::RSA_PUB_KEY_END_STRING) === false) {
            // add rsa footer to key string
            $publicKey = $publicKey . self::RSA_PUB_KEY_END_STRING;
        }

        return $publicKey;
    }
}
