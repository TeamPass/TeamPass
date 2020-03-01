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

use Neos\Flow\Annotations as Flow;
use TeamPass\Core\Domain\Model\EncryptedContent;
use TeamPass\Core\Domain\Model\Acl;
use TeamPass\Core\Domain\Model\User;
use TeamPass\Core\Domain\Model\UserGroup;
use TeamPass\Core\Domain\Model\GroupElement;
use TeamPass\Core\Domain\Model\GroupTreeElement;
use TeamPass\Core\Domain\Model\IntermediateKey;
use TeamPass\Core\Domain\Model\ElementTemplate;
use TeamPass\Core\Domain\Dto\Element;

/**
 * Class EncryptionService
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

class EncryptionService extends AbstractService
{
    /**
     * @var int
     */
    protected const RANDOMKEYLENGTH = 24;

    /**
     * @Flow\Inject
     * @var WorkQueueService
     */
    protected $workQueueService;

    /**
     * @Flow\Inject
     * @var SettingService
     */
    protected $settingService;

    /**
     * Returns decrypted content for user
     *
     * @param integer $userId    the user id
     * @param integer $elementId the element id
     * @param string  $aesKey    the plain aes key
     *
     * @return string
     * @throws \Exception
     */
    public function getEncryptedElementForUser(int $userId, int $elementId, string $aesKey): string
    {
        /** @var GroupElement $groupElement */
        $groupElement = $this->groupElementRepository->load($elementId);

        /** @var EncryptedContent $encryptedContent */
        $encryptedContent = $groupElement->getCurrentEncryptedEntity();

        /** @var IntermediateKey $key */
        $key = $this->intermediateKeyRepository->getIntermediateKeyForUser($userId, $elementId);

        $contentKey = $this->aesDecrypt($key->getEncryptedAesKey(), $aesKey);
        $contentKey = json_decode($contentKey);
        $content = $this->aesDecrypt($encryptedContent->getContent(), $contentKey->key);

        if (!$content) {
            throw new \Exception("decryption not successful");
        }

        return $content;
    }

    /**
     * encrypts given content for given element
     *
     * @param Element $element the element value object
     * @param integer $userId  the user id
     *
     * @return array
     * @throws \Exception
     */
    public function encryptElement(Element $element, int $userId): array
    {
        /** @var GroupElement $groupElement */
        $groupElement = $this->groupElementRepository->load($element->getId());

        /** @var ElementTemplate $template */
        /** @scrutinizer ignore-call */
        $template =  $this->elementTemplateRepository->findOneByInternalName($element->getTemplateName());

        $result["success"] = true;
        $result["isEncrypted"] = true;

        if (!$groupElement->getCurrentEncryptedEntity()) {
            $this->logger->debug("user {$userId} trys to create a first encrypted content for ge {$element->getId()}");
            if ($element->getAesKey() !== false) {
                $this->logger->debug("client has sent an aes key but this shouldn't exist");
            }

            // generate a random key for encrypting json content
            $aesKey = $this->generateRandomKey(50);

            $encryptedJsonContent = $this->aesEncrypt($element->getDecryptedContent(), $aesKey);

            $encryptedContent = new EncryptedContent();
            $encryptedContent->setContent($encryptedJsonContent);
            $encryptedContent->setGroupElement($groupElement);
            $encryptedContent->setTemplate($template);

            $this->encryptedContentRepository->add($encryptedContent);
            $this->persistenceManager->persistAll();

            //init encryption for all users
            $this->initEncryptionNewKey($element->getId(), $aesKey);

            /** @var IntermediateKey $key */
            $key = $this->intermediateKeyRepository->getIntermediateKeyForUser($userId, $element->getId());

            $result['rsaEncAesKey'] = $key->getRsaEncryptedAesKey();
        } else {
            /** @var IntermediateKey $key */
            $key = $this->intermediateKeyRepository->getIntermediateKeyForUser($userId, $element->getId());

            if (!$key instanceof IntermediateKey) {
                throw new \Exception("the key for encrypted content does not exist");
            }

            if ($element->getAesKey() === false) {
                throw new \Exception("no intermediate aes key was sent by client");
            }

            $contentAesKey = $this->aesDecrypt($key->getEncryptedAesKey(), $element->getAesKey());
            if (!$contentAesKey) {
                throw new \Exception("decrypting intermediate key failed");
            }
            $contentAesKey = json_decode($contentAesKey);

            $encryptedJsonContent = $this->aesEncrypt($element->getDecryptedContent(), $contentAesKey->key);

            $encryptedContent = new EncryptedContent();
            $encryptedContent->setContent($encryptedJsonContent);
            $encryptedContent->setGroupElement($groupElement);
            $encryptedContent->setTemplate($template);

            $this->encryptedContentRepository->add($encryptedContent);
            $this->persistenceManager->persistAll();
        }

        $this->groupElementRepository->update($groupElement);

        return $result;
    }

    /**
     * creates all intermediate keys for a newly created encrypted content entity
     *
     * @param integer $elementId          the element id
     * @param string  $encryptionKey      the encryption key
     *
     * @return bool
     * @throws \Exception
     */
    public function initEncryptionNewKey(int $elementId, string $encryptionKey): bool
    {
        // cache every userId for users getting a new intermediate key, to prevent double encryption for admin users
        $userCache = array();
        $groupElement = $this->groupElementRepository->load($elementId);
        /** @var GroupTreeElement $groupTreeElement */
        $groupTreeElement = $groupElement->getGroupTreeElement();
        $acls = $groupTreeElement->getParent()->getAcls();

        /** @var Acl $acl */
        foreach ($acls as $acl) {
            if ($acl->getRead()) {
                /** @var User $user */
                foreach ($acl->getGroup()->getUsers() as $user) {
                    if (!isset($userCache[$user->getUserId()])) {
                        $this->createIntermediateKeyForUser($user, $groupElement, $encryptionKey);
                        $userCache[$user->getUserId()] = true;
                    }
                }
            }
        }

        $this->createIntermediateKeyForAdmins($userCache, $groupElement, $encryptionKey);

        return true;
    }

    /**
     * encrypt EncryptionKey for all admin users
     *
     * @param array        $userCache     all user ids which already has been encrypted (prevent multiple
     * encryptions for same user)
     * @param GroupElement $groupElement  the group element
     * @param string       $encryptionKey the encryption key of the encrypted content
     *
     * @return void
     * @throws \Exception
     */
    protected function createIntermediateKeyForAdmins(
        array $userCache,
        GroupElement $groupElement,
        string $encryptionKey
    ): void {
        /** @scrutinizer ignore-call */
        $groups = $this->userGroupRepository->findByAdmin(1);

        /** @var UserGroup $group */
        foreach ($groups as $group) {
            /** @var User $user */
            foreach ($group->getUsers() as $user) {
                if (!isset($userCache[$user->getUserId()])) {
                    $this->createIntermediateKeyForUser($user, $groupElement, $encryptionKey);
                    $userCache[$user->getUserId()] = true;
                }
            }
        }
    }

    /**
     * creates a intermediate key for given user and encryptionkey
     *
     * @param User         $user          the user instance
     * @param GroupElement $groupElement  the group element instance
     * @param string       $encryptionKey the encryption key
     *
     * @return void
     * @throws \Exception
     */
    protected function createIntermediateKeyForUser(User $user, GroupElement $groupElement, string $encryptionKey): void
    {
        if ($user->getPublicKey()) {
            $this->logger->debug("creating new intermediate key for user: {$user->getUsername()}");
            // generate a new random key to encrypt the first random key plus a salt
            $randomKey = $this->generateRandomKey();

            // create a array containing clear aes key an a salt
            $tmp['salt'] = $user->getUsername();
            $tmp['key'] = $encryptionKey;

            $encAesKey1 = $this->aesEncrypt(json_encode($tmp), $randomKey);

            // encrypt this random key with user's public key
            $encAesKey2 = $this->rsaEncrypt($randomKey, $user->getPublicKey());

            $ik = new IntermediateKey();
            $ik->setGroupElement($groupElement)
                ->setUser($user)
                ->setEncryptedAesKey($encAesKey1)
                ->setRsaEncryptedAesKey($encAesKey2);

            $this->intermediateKeyRepository->add($ik);
            $this->persistenceManager->persistAll();
        } else {
            $this->logger->error("User '{$user->getUsername()}' has no key pair in database");
        }
    }

    /**
     * persists given aes keys for given user
     *
     * @param array $aesKeys     array containing user-id, group-element-id and aes-keys per row
     * @param int   $adminUserId the user id which initiated this encryption
     *
     * @return void
     * @throws \Exception
     */
    public function saveAesKeys(array $aesKeys, int $adminUserId): void
    {
        foreach ($aesKeys as $row) {
            $userId = $row['userId'];
            $groupElementId = $row['groupElementId'];
            $aesKey = $row['aesKey'];

            $user = $this->userRepository->load($userId);
            $groupElement = $this->groupElementRepository->load($groupElementId);

            $intermediateKey = $this->intermediateKeyRepository->getIntermediateKeyForUser(
                $adminUserId,
                $groupElementId
            );

            $contentKey = $this->aesDecrypt($intermediateKey->getEncryptedAesKey(), $aesKey);
            $contentKey = json_decode($contentKey);

            $this->createIntermediateKeyForUser($user, $groupElement, $contentKey->key);
        }
    }

    /**
     * Returns a list of aesKeys for Elements that are not encrypted for given user
     *
     * @param int $adminUserId administrators user id
     * @param int $userId      id of user for encryption
     * @param int|null $limit       limit of aesKeys returned in one batch
     *
     * @return array|bool
     */
    public function getBatchedAesKeys(int $adminUserId, int $userId, ?int $limit = null)
    {
        try {
            $limit = (int) $this->settingService->get('encryption.batchSize');

            $groupElements = $this->getGroupElementsNotEncryptedForUser($userId, $limit);

            $result = array();

            /** @var GroupElement $groupElement */
            foreach ($groupElements as $groupElement) {
                $intermediateKey = $this->intermediateKeyRepository->getIntermediateKeyForUser(
                    $adminUserId,
                    $groupElement->getId()
                );

                $result[] = array(
                    "userId" => $userId,
                    "groupElementId" => $groupElement->getId(),
                    "rsaKey" => $intermediateKey->getRsaEncryptedAesKey()
                );
            }

            if (count($result) === 0) {
                $this->logger->debug("deleting User {$userId} from Queue");
                $this->workQueueService->deleteUserFromWorkQueue($userId);

                return false;
            } else {
                return $result;
            }
        } catch (\Exception $e) {
            $this->logger->error((string)$e);
        }

        return false;
    }

    /**
     * returns a array containing group elements which are not encrypted for given user
     *
     * @param int $userId the requested user
     * @param int $limit  max amount of returned groupelements
     *
     * @return array
     * @throws \Exception
     */
    protected function getGroupElementsNotEncryptedForUser(int $userId, int $limit): array
    {
        // all group elements which already has an encrypted content reference for user
        $encryptedGroupElementIds = $this->getUsersCurrentIntermediateKeyGroupElementIds($userId);

        // all group elements user is allowed to access
        $currentGroupElements = $this->getUsersCurrentGroupElements($userId);

        $result = array();
        $counter = 0;

        foreach ($currentGroupElements as $elementKey => $element) {
            if ($counter === $limit) {
                break;
            }

            // if element is not encrypted for user
            if (!isset($encryptedGroupElementIds[$elementKey])) {
                $result[] = $element;
                $counter++;
            }
        }
        return $result;
    }

    /**
     * generates a new salt and returns it
     *
     * @param int $length random key length
     *
     * @return string
     * @throws \Exception
     */
    protected function generateRandomKey(int $length = self::RANDOMKEYLENGTH): string
    {
        try {
            $randomBytes = random_bytes(128);
        } catch (\Exception $e) {
            $randomBytes = openssl_random_pseudo_bytes(128);
        }

        $randomBytesInHex = bin2hex($randomBytes);

        return substr($randomBytesInHex, 0, $length);
    }

    /**
     * generates a array containing all groupElements for given user
     *
     * @param integer $userId the user id
     *
     * @return array $elements array containing all groupElements for given user
     * @throws \Exception
     */
    protected function getUsersCurrentGroupElements(int $userId): array
    {
        $result = array();

        /** @User $user  */
        $user = $this->userRepository->load($userId);

        // if user is admin, simple return all existing group elements
        if ($this->aclRepository->isAdmin($user->getUserId(), false)) {
            $elements = $this->groupElementRepository->findAll();

            /** @var GroupElement $element */
            foreach ($elements as $element) {
                if ($element->getCurrentEncryptedEntity() instanceof EncryptedContent) {
                    $result[$element->getId()] = $element;
                }
            }

            return $result;
        }

        foreach ($user->getGroups() as $userGroup) {
            /** @var UserGroup $userGroup */
            foreach ($userGroup->getAcls() as $acl) {
                /** @var $acl Acl */
                if ($acl->getRead()) {
                    /** @var $gte GroupTreeElement */
                    $gte = $acl->getGroupTreeElement();
                    /** @var $child GroupTreeElement */
                    foreach ($gte->getChildren() as $child) {
                        if ($child->getLeaf() == 1) {
                            /** @var GroupElement $element */
                            foreach ($child->getElements() as $element) {
                                if ($element->getCurrentEncryptedEntity() instanceof EncryptedContent) {
                                    $result[$element->getId()] = $element;
                                } else {
                                    $this->logger->debug("element {$element->getId()} has no encrypted content");
                                }
                            }
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * delete all intermediate keys where user is not allowed to use. If $elements parameter is not set
     * all group elements will checked if a key exists
     *
     * @param int $userId the user id
     *
     * @return void
     * @throws \Exception
     */
    public function removeIntermediateKeysOnACLChange(int $userId): void
    {
        $this->logger->debug("searching for intermediate keys to remove for user {$userId}");

        /** @var array $elements */
        $elements = $this->getUsersCurrentGroupElements($userId);

        /** @var array $intermediateKeys */
        $intermediateKeys = $this->getUsersIntermediateKeys($userId);

        /** @var IntermediateKey $intermediateKey */
        foreach ($intermediateKeys as $intermediateKey) {
            if (!isset($elements[$intermediateKey->getGroupElement()->getId()])) {
                $this->logger->debug("intermediate key of GroupElement
                '{$intermediateKey->getGroupElement()->getName()}' for user '{$userId}' will be deleted now");

                $this->intermediateKeyRepository->remove($intermediateKey);
            }
        }
    }

    /**
     * gets all acl related users and call "removeIntermediateKeysOnACLChange" to check if some
     * intermediate keys are need to be removed
     *
     * @param integer $userGroupId the user group id of changed acl
     *
     * @return void
     * @throws \Exception
     */
    public function removeIntermediateKeysOnACLChangeByUserGroup(int $userGroupId): void
    {
        /** @var UserGroup $userGroup */
        $userGroup = $this->userGroupRepository->load($userGroupId);

        /** @var User $user */
        foreach ($userGroup->getUsers() as $user) {
            // delete intermediate key only if user is not member in at least one group with admin privileges
            if (!$this->aclRepository->isAdmin($user->getUserId(), false)) {
                $this->removeIntermediateKeysOnACLChange($user->getUserId());
            }
        }
    }

    /**
     * checks if every user for given group tree element has all encrypted elements respectively not more
     * normally called by message queue!
     *
     * @param GroupTreeElement $groupTreeElement the group tree element
     *
     * @return void
     * @throws \Exception
     */
    public function updateIntermediateKeysForGte(GroupTreeElement $groupTreeElement): void
    {
        // all elements within given group tree element
        /** @var array $elements */
        $elements = $this->getAllGroupElementsForGroupTreeElement($groupTreeElement);

        // all users having read permission on given group tree element
        /** @var array $users */
        $users = $this->getAllUsersWithReadPermissionOnGroupTreeElement($groupTreeElement);

        foreach ($elements as $element) {
            /** @var array $intermediateKeys */
            $intermediateKeys = $this->getAllIntermediateKeysForElement($element);
            // holds all userIds of users which having a intermediate key for this element
            $userWithIk = array();

            /** @var IntermediateKey $ik */
            foreach ($intermediateKeys as $ik) {
                // delete intermediate key for a user which hasn't read permissions (user is not in users array)
                if (!isset($users[$ik->getUser()->getUserId()])) {
                    $this->intermediateKeyRepository->remove($ik);
                } else {
                    // save user id as array key an "true" as dummy value
                    $userWithIk[$ik->getUser()->getUserId()] = true;
                }
            }

            /** @var User $user */
            foreach ($users as $user) {
                // add user to work queue if no intermediate key was found in array
                if (!isset($userWithIk[$user->getUserId()])) {
                    $this->workQueueService->addToWorkQueueSilent($user->getUserId());
                }
            }
        }
    }

    /**
     * deletes unused intermediateKeys and/or adds user to work queue if some keys requires to become encrypted
     * for all group members
     *
     * @param int $userGroupId the userGroup id
     *
     * @return void
     * @throws \Exception
     */
    public function updateIntermediateKeysForUserGroup(int $userGroupId): void
    {
        $userGroup = $this->userGroupRepository->load($userGroupId);

        /** @var User $user */
        foreach ($userGroup->getUsers() as $user) {
            $this->updateIntermediateKeysForUser($user->getUserId());
        }
    }

    /**
     * deletes unused intermediateKeys and/or adds user to work queue if some keys requires to become encrypted
     * for a specific given user
     *
     * @param int $userId the user id
     *
     * @return void
     * @throws \Exception
     */
    public function updateIntermediateKeysForUser(int $userId): void
    {
        /** @var array $elements */
        $elements = $this->getUsersCurrentGroupElements($userId);

        /** @var array $intermediateKeys */
        $intermediateKeys = $this->getAllIntermediateKeysForUser($userId);

        // helper method which returns only the group element ids of user's current intermediate keys as array key
        /** @var array $elementIdsForIks */
        $elementIdsForIks = $this->getUsersCurrentIntermediateKeyGroupElementIds($userId);

        // delete intermediate keys
        /** @var IntermediateKey $ik */
        foreach ($intermediateKeys as $ik) {
            // delete intermediate key for a user which hasn't read permissions (element is not in elements array)
            if (!isset($elements[$ik->getGroupElement()->getId()])) {
                $this->logger->debug("deleting ik {$ik->getId()} for user {$userId}");
                $this->intermediateKeyRepository->remove($ik);
            }
        }

        // add user to work queue if no intermediate key was found in array
        /** @var GroupElement $element */
        foreach ($elements as $element) {
            if (!isset($elementIdsForIks[$element->getId()])) {
                $this->logger->debug("adding user {$userId} to work queue3");
                $this->workQueueService->addToWorkQueueSilent($userId);

                // user needs to be added only one time to work queue, so we can leave the loop here
                break;
            }
        }
    }

    /**
     * returns a array containing all current existing intermediate keys for given user.
     * the array key is equal to intermediate key id in database
     *
     * @param int $userId the user id
     *
     * @return array
     * @throws \Exception
     */
    protected function getAllIntermediateKeysForUser(int $userId): array
    {
        $result = array();
        $user = $this->userRepository->load($userId);

        /** @scrutinizer ignore-call */
        $intermediateKeys = $this->intermediateKeyRepository->findByUser($user);

        /** @var IntermediateKey $intermediateKey */
        foreach ($intermediateKeys as $intermediateKey) {
            $result[$intermediateKey->getId()] = $intermediateKey;
        }

        return $result;
    }

    /**
     * returns a array containing all intermediate keys related to given group element
     *
     * @param GroupElement $element the group element
     *
     * @return mixed
     */
    protected function getAllIntermediateKeysForElement(GroupElement $element)
    {
        /** @scrutinizer ignore-call */
        $intermediateKeys = $this->intermediateKeyRepository->findByGroupElement($element);

        return $intermediateKeys;
    }

    /**
     * Returns a array containing all group elements having a valid intermediate key for given user
     *
     * @param int $userId the user id
     *
     * @return array $result the group element ids as array keys with dummy value true
     */
    protected function getUsersIntermediateKeys(int $userId): array
    {
        $result = array();

        /** @scrutinizer ignore-call */
        $intermediateKeys = $this->intermediateKeyRepository->findByUser($userId);

        /** @var IntermediateKey $ik */
        foreach ($intermediateKeys as $ik) {
            $result[$ik->getId()] = $ik;
        }

        return $result;
    }

    /**
     * Returns a array containing all group elements having a valid intermediate key for given user
     *
     * @param int $userId the user id
     *
     * @return array $result the group element ids as array keys with dummy value true
     */
    protected function getUsersCurrentIntermediateKeyGroupElementIds(int $userId): array
    {
        $result = array();

        /** @scrutinizer ignore-call */
        $intermediateKeys = $this->intermediateKeyRepository->findByUser($userId);

        /** @var IntermediateKey $ik */
        foreach ($intermediateKeys as $ik) {
            $result[$ik->getGroupElement()->getId()] = true;
        }

        return $result;
    }

    /**
     * returns a array containing all users having read permissions on given group tree element
     *
     * @param GroupTreeElement $groupTreeElement the group tree element
     *
     * @return array
     */
    public function getAllUsersWithReadPermissionOnGroupTreeElement(GroupTreeElement $groupTreeElement): array
    {
        $result = array();

        if ($groupTreeElement->getLeaf()) {
            $groupTreeElement = $groupTreeElement->getParent();
        }

        foreach ($groupTreeElement->getAcls() as $acl) {
            if ($acl->getRead()) {
                /** @var User $user */
                /** @var Acl $acl */
                foreach ($acl->getGroup()->getUsers() as $user) {
                    $result[$user->getUserId()] = $user;
                }
            }
        }

        // add all admin accounts to list
        $admins = $this->getAllAdminUsers();

        return array_replace($result, $admins);
    }

    /**
     * get a list of admin users
     *
     * @return array
     */
    protected function getAllAdminUsers(): array
    {
        $users = array();
        /** @scrutinizer ignore-call */
        $groups = $this->userGroupRepository->findByAdmin(1);

        /** @var UserGroup $group */
        foreach ($groups as $group) {
            /** @var User $user */
            foreach ($group->getUsers() as $user) {
                $users[$user->getUserId()] = $user;
            }
        }

        return $users;
    }

    /**
     * returns a array containing all group elements of all leaf group tree elements of
     * given non-leaf group tree element
     *
     * @param GroupTreeElement $groupTreeElement the group tree element
     *
     * @return array
     */
    protected function getAllGroupElementsForGroupTreeElement(GroupTreeElement $groupTreeElement): array
    {
        $result = array();
        /** @var GroupTreeElement $child */
        if (!$groupTreeElement->getLeaf()) {
            foreach ($groupTreeElement->getChildren() as $child) {
                if ($child->getLeaf()) {
                    /** @var GroupElement $element */
                    foreach ($child->getElements() as $element) {
                        if ($element->getCurrentEncryptedEntity() instanceof EncryptedContent) {
                            $result[$element->getId()] = $element;
                        } else {
                            $this->logger->debug("element {$element->getId()} has no encrypted content");
                        }
                    }
                }
            }
        } else {
            /** @var GroupElement $element */
            foreach ($groupTreeElement->getElements() as $element) {
                if ($element->getCurrentEncryptedEntity() instanceof EncryptedContent) {
                    $result[$element->getId()] = $element;
                } else {
                    $this->logger->debug("element {$element->getId()} has no encrypted content");
                }
            }
        }

        return $result;
    }
}
