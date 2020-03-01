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

namespace TeamPass\ApiV1\Service;

use Neos\Flow\Persistence\Doctrine\PersistenceManager;
use TeamPass\Core\Domain\Repository\AclRepository;
use TeamPass\Core\Domain\Repository\DirectoryRepository;
use TeamPass\Core\Domain\Repository\ElementTemplateRepository;
use TeamPass\Core\Domain\Repository\EncryptedContentRepository;
use TeamPass\Core\Domain\Repository\GroupElementRepository;
use TeamPass\Core\Domain\Repository\GroupTreeElementRepository;
use TeamPass\Core\Domain\Repository\IntermediateKeyRepository;
use TeamPass\Core\Domain\Repository\SettingRepository;
use TeamPass\Core\Domain\Repository\UserGroupRepository;
use Psr\Log\LoggerInterface;
use GibberishAES\GibberishAES;
use TeamPass\Core\Domain\Repository\UserRepository;
use TeamPass\Core\Domain\Repository\WorkQueueRepository;
use Neos\Flow\Annotations as Flow;

/**
 * Class AbstractService
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

class AbstractService
{
    /**
     * @Flow\Inject
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @Flow\Inject
     * @var PersistenceManager
     */
    protected $persistenceManager;

    /**
     * @Flow\Inject
     * @var GroupElementRepository
     */
    protected $groupElementRepository;

    /**
     * @Flow\Inject
     * @var GroupTreeElementRepository
     */
    protected $groupTreeElementRepository;

    /**
     * @Flow\Inject
     * @var UserGroupRepository
     */
    protected $userGroupRepository;

    /**
     * @Flow\Inject
     * @var AclRepository
     */
    protected $aclRepository;

    /**
     * @Flow\Inject
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @Flow\Inject
     * @var DirectoryRepository
     */
    protected $directoryRepository;

    /**
     * @Flow\Inject
     * @var IntermediateKeyRepository
     */
    protected $intermediateKeyRepository;

    /**
     * @Flow\Inject
     * @var ElementTemplateRepository
     */
    protected $elementTemplateRepository;

    /**
     * @Flow\Inject
     * @var EncryptedContentRepository
     */
    protected $encryptedContentRepository;

    /**
     * @Flow\Inject
     * @var SettingRepository
     */
    protected $settingRepository;

    /**
     * @Flow\Inject
     * @var WorkQueueRepository
     */
    protected $workQueueRepository;

    /**
     * encrypt content with given key
     *
     * @param string $content content to encrypt
     * @param string $aesKey  encrypt content with this key
     *
     * @return mixed
     */
    protected function aesEncrypt(string $content, string $aesKey)
    {
        return GibberishAES::enc($content, $aesKey);
    }

    /**
     * decrypt content with given key
     *
     * @param string $encryptedContent content to decrypt
     * @param string $aesKey           encrypt content with this key
     *
     * @return mixed
     */
    protected function aesDecrypt(string $encryptedContent, string $aesKey)
    {
        return GibberishAES::dec($encryptedContent, $aesKey);
    }

    /**
     * encrypts given content with given rsa public key
     *
     * @param string $content   the content to encrypt
     * @param string $publicKey the rsa public key
     *
     * @return string
     */
    protected function rsaEncrypt(string $content, string $publicKey): string
    {
        openssl_public_encrypt($content, $output, $publicKey);
        return base64_encode($output);
    }

    /**
     * decrypts given content with given rsa private key
     *
     * @param string $encryptedContent the encrypted content
     * @param string $privateKey       the users private key
     *
     * @return mixed
     */
    protected function rsaDecrypt(string $encryptedContent, string $privateKey)
    {
        openssl_private_decrypt(base64_decode($encryptedContent), $output, $privateKey);

        return $output;
    }
}
