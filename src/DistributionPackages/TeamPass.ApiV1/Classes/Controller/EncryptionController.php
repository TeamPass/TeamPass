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

use Neos\Flow\Annotations as Flow;
use TeamPass\Core\Domain\Dto\Element;
use TeamPass\Core\Domain\Dto\Encrypted;
use TeamPass\Core\Domain\Dto\Handshake;
use TeamPass\Core\Exception\RequestValidationException;
use TeamPass\ApiV1\Service\AclService;
use TeamPass\ApiV1\Service\EncryptionService;
use TeamPass\ApiV1\Service\UserService;

/**
 * Class EncryptionController
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

class EncryptionController extends ProtectedController
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
     * @var string
     */
    protected const RSA_DIGEST_ALG = "sha512";

    /**
     * @var int
     */
    protected const RSA_PRIVATE_KEY_BITS = 2048;

    /**
     * @var int
     */
    protected const RSA_PRIVATE_KEY_TYPE = OPENSSL_KEYTYPE_RSA;

    /**
     * @var int
     */
    protected const AES_DEFAULT_SIZE = 256;

    /**
     * @Flow\Inject
     * @var AclService
     */
    protected $aclService;

    /**
     * @Flow\Inject
     * @var UserService
     */
    protected $userService;

    /**
     * @Flow\Inject
     * @var EncryptionService
     */
    protected $encryptionService;

    /**
     * initialize startHandshake action
     *
     * @return void
     * @throws \Neos\Flow\Mvc\Exception\NoSuchArgumentException
     */
    protected function initializeStartHandshakeAction(): void
    {
        $this->abstractInitialize('handshake', ["publicKey"]);
    }

    /**
     * start initial Handshake: encrypt a long random key with client's public key, generate a serverSide keypair,
     * store Keys and encrypted random key to session and send server's public key and encrypted random key to client
     *
     * @param Handshake $handshake
     *
     * @Flow\ValidationGroups({"EncryptionControllerStartHandshakeAction"})
     * @return void
     * @throws \Exception
     */
    public function startHandshakeAction(Handshake $handshake): void
    {
        $clientPublicKey = $handshake->getPublicKey();

        // generate a random key and encrypt it with the user's public key
        $source = $this->generateRandomKey(50);
        $clientEncryptSource = $this->rsaEncrypt($source, $clientPublicKey);

        $handshakeToken = $this->generateRandomKey(25);
        $encryptedHandshakeToken = $this->rsaEncrypt($handshakeToken, $clientPublicKey);

        $this->session->putData("handshakeToken", $handshakeToken);
        $this->session->putData("sessionAesKey", $source);

        $result = [
                'success' => true,
                'result' => array (
                    'encryptedSessionAesKey' => $clientEncryptSource,
                    'encryptedHandshakeToken' => $encryptedHandshakeToken
                )
            ];

        $this->view->assign('value', $result);
    }

    /**
     * initialize ackHandshake action
     *
     * @return void
     * @throws \Neos\Flow\Mvc\Exception\NoSuchArgumentException
     */
    protected function initializeAckHandshakeAction(): void
    {
        $this->abstractInitialize('handshake', ["handshakeToken"]);
    }

    /**
     * acknowledge handshake
     *
     * @param Handshake $handshake
     *
     * @Flow\ValidationGroups({"EncryptionControllerAckHandshakeAction"})
     * @return void
     * @throws \Exception
     */
    public function ackHandshakeAction(Handshake $handshake): void
    {
        $clientHandshakeToken = $handshake->getHandshakeToken();

        $handshakeToken = $this->session->getData("handshakeToken");

        if ($clientHandshakeToken === $handshakeToken) {
            $this->session->putData("encryptionAcknowledged", true);

            $userId = (int) $this->session->getData("userId");
            $user = $this->userService->get($userId);
            $privateKey = $user->getPrivateKey();

            if ($privateKey) {
                $result = array ("validRsaState" => true, "aesPrivateKey" => $privateKey);
            } else {
                $result = array ("validRsaState" => false);
            }
            $result = json_encode($result);

            $encryptedResult = $this->aesEncrypt($result);

            $result = [
                'success' => true,
                'result' => array (
                    "privateKey" => $encryptedResult
                )
            ];

            $this->view->assign('value', $result);
        } else {
            throw new RequestValidationException("incomplete handshake");
        }
    }

    /**
     * initialize setUserKeyPair action
     *
     * @return void
     * @throws \Neos\Flow\Mvc\Exception\NoSuchArgumentException
     */
    protected function initializeSetUserKeyPairAction(): void
    {
        $this->abstractInitialize('encrypted', ['publicKey', 'privateKey']);
    }

    /**
     * setting up key pair
     *
     * @param Encrypted $encrypted
     *
     * @Flow\ValidationGroups({"EncryptionControllerSetUserKeyPairAction"})
     * @return void
     * @throws \Exception
     */
    public function setUserKeyPairAction(Encrypted $encrypted): void
    {
        $userId = (int) $this->session->getData("userId");
        $this->userService->updateRsaKeyPair($userId, $encrypted->getPublicKey(), $encrypted->getPrivateKey());

        $this->view->assign('value', ['success' => true, 'result' => true]);
    }

    /**
     * initialize updateUserPrivateKey action
     *
     * @return void
     * @throws \Neos\Flow\Mvc\Exception\NoSuchArgumentException
     */
    protected function initializeUpdateUserPrivateKeyAction(): void
    {
        $this->abstractInitialize('encrypted', ['privateKey']);
    }


    /**
     * update the private key
     *
     * @param Encrypted $encrypted
     *
     * @Flow\ValidationGroups({"EncryptionControllerUpdateUserPrivateKeyAction"})
     * @return void
     * @throws \Exception
     */
    public function updateUserPrivateKeyAction(Encrypted $encrypted): void
    {
        $userId = (int) $this->session->getData("userId");

        $this->userService->updatePrivateKey($userId, $encrypted->getPrivateKey());

        $this->view->assign('value', ['success' => true, 'result' => true]);
    }

    protected function initializeGetEncryptedElementAction()
    {
        $this->abstractInitialize('element', ['aesKey', 'elementId']);
        // $configuration = $this->arguments->getArgument('element')->getPropertyMappingConfiguration();
        // $configuration->allowAllProperties();
        // $configuration->setTypeConverter(new ElementTypeConverter());
        // $configuration->setTypeConverterOption(ElementTypeConverter::class, "aesKey",$this->sessionAesKey());
    }

    /**
     * returns one encrypted element
     *
     * @param Element $element
     *
     * @Flow\ValidationGroups({"EncryptionControllerGetEncryptedElementAction"})
     * @return void
     * @throws \Exception
     */
    public function getEncryptedElementAction(Element $element): void
    {
        $userId = (int) $this->session->getData("userId");

        $this->aclService->checkPermissions($userId, "groupElement", "read", $element->getElementId());

        $result = $this->encryptionService->getEncryptedElementForUser(
            $userId,
            $element->getElementId(),
            $element->getAesKey()
        );

        $result = $this->aesEncrypt($result);

        $this->view->assign('value', ['success' => true, 'result' => ['encryptedContent' => $result]]);
    }

    /**
     * initialize updateElementAction method
     *
     * @throws \Neos\Flow\Mvc\Exception\NoSuchArgumentException
     */
    protected function initializeUpdateElementAction()
    {
        $this->abstractInitialize('element', ['elementId', 'aesKey', 'template', 'decryptedContent']);
    }

    /**
     * updates a element
     *
     * @param Element $element
     *
     * @Flow\ValidationGroups({"EncryptionControllerUpdateElementAction"})
     * @return void
     * @throws \Exception
     */
    public function updateElementAction(Element $element): void
    {
        $userId = (int) $this->session->getData("userId");
        $this->aclService->checkPermissions($userId, "groupElement", "update", $element->getElementId());

        $result = $this->encryptionService->encryptElement($element, $userId);

        $this->view->assign('value', ['success' => true, 'result' => $result]);
    }
}
