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

use TeamPass\Core\Domain\Model\User;
use TeamPass\Core\Domain\Model\UserGroup;
use TeamPass\Core\Domain\Model\Directory;
use TeamPass\Core\Domain\Model\GroupTreeElement;
use TeamPass\Core\Domain\Model\Acl;
use TeamPass\Core\Exception\InvalidNewPasswordException;
use TeamPass\Core\Exception\InvalidOldPasswordException;
use TeamPass\Core\Exception\InvalidRepeatPasswordException;
use TeamPass\Core\Exception\RequestValidationException;
use TeamPass\Core\Domain\Dto\Person;
use TeamPass\Core\Exception\UserException;
use TeamPass\Core\Util\Keys;
use Doctrine\ORM\NonUniqueResultException;
use Neos\Flow\Annotations as Flow;

/**
 * Class UserService
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

class UserService extends AbstractService
{
    /**
     * @Flow\Inject
     * @var WorkQueueService
     */
    protected $workQueueService;

    /**
     * @Flow\Inject
     * @var EncryptionService
     */
    protected $encryptionService;

    /**
     * @Flow\Inject
     * @var DirectoryService
     */
    protected $directoryService;

    /**
     * @Flow\Inject
     * @var SettingService
     */
    protected $settingService;

    /**
     * creates a new user object
     *
     * @param Person $person the person dto
     * @param string $group the group name
     *
     * @return array
     * @throws \Exception
     */
    public function create(Person $person, ?string $group = null): array
    {
        /** @scrutinizer ignore-call */
        $result = $this->userRepository->findOneByUsername($person->getUsername());
        if ($result) {
            throw new UserException("a User with username '{$person->getUsername()}' already exists");
        }

        $userGroup = null;
        if ($group) {
            /** @scrutinizer ignore-call */
            $userGroup = $this->userGroupRepository->findOneByName($group);

            // if no group was found we stop here
            if (!$userGroup instanceof UserGroup) {
                throw new UserException("a group named '{$group}' does not exist");
            }
        }

        /** @var User $user */
        $user = new User();

        if ($person->getNewPassword()) {
            // check for password requirements
            if (!$this->validatePassword($person->getNewPassword())) {
                throw new UserException("Password not matches the requirements");
            }
            $user->setPassword($person->getNewPassword());
        } else {
            // empty password is allowed but user is disabled by force
            $person->setEnabled(false);
        }

        // get the local directory - there can only be one at a time
        /** @var Directory $directory */
        $directory = $this->directoryService->getLocalDirectory();

        $user->setEmail($person->getEmailAddress());
        $user->setUsername($person->getUsername());
        $user->setFullName($person->getFullName());
        $user->setDirectory($directory);
        if ($person->isEnabled()) {
            $user->enable();
        } else {
            $user->disable();
        }

        // only if userGroup is set we need add the user a group
        if ($userGroup) {
            // add user to given group
            $user->addToGroup($userGroup);
        }

        $this->userRepository->add($user);
        $this->persistenceManager->persistAll();

        return $user->asArray();
    }

    /**
     * Returns all users
     *
     * @return array
     */
    public function getAllUsersAsGrid(): array
    {
        $users = $this->userRepository->findAll();
        $result = array();

        /** @var  User $user */
        foreach ($users as $user) {
            $result[] = $user->asArray();
        }

        return $result;
    }

    /**
     * Update Users key pair
     *
     * @param integer $userId     the user id
     * @param string  $publicKey  the rsa public key
     * @param string  $privateKey the rsa private key (aes encrypted)
     *
     * @return void
     * @throws \Exception
     */
    public function updateRsaKeyPair(int $userId, string $publicKey, string $privateKey): void
    {
        /** @var User $user */
        $user = $this->userRepository->load($userId);

        $user->setPublicKey($publicKey);
        $user->setPrivateKey($privateKey);

        $this->userRepository->update($user);

        $this->workQueueService->addToWorkQueueSilent($user->getUserId());
    }

    /**
     * Update Users private key
     *
     * @param integer $userId     the user id
     * @param string  $privateKey the rsa private key
     *
     * @return void
     * @throws \Exception
     */
    public function updatePrivateKey(int $userId, string $privateKey): void
    {
        /** @var User $user */
        $user = $this->userRepository->load($userId);

        $user->setPrivateKey($privateKey);

        $this->userRepository->update($user);
    }

    /**
     * Returns all GroupTreeElements where user has at least read permissions
     *
     * @param integer $userId the user id
     *
     * @return array
     * @throws \Exception
     */
    public function findAllGroupTreeElementsByPermission(int $userId): array
    {
        if ($this->isAdmin($userId)) {
            $ar = array();

            // get all non leaf group tree elements
            /** @scrutinizer ignore-call */
            $groupTreeElements = $this->groupTreeElementRepository->findByLeaf(false);

            /** @var $gte GroupTreeElement */
            foreach ($groupTreeElements as $gte) {
                $tmpar = array();
                $tmpar["id"] = $gte->getGroupTreeElementId();
                $tmpar["isRoot"] = $gte->getRoot();
                $tmpar["name"] = $gte->getName();
                $tmpar["index"] = $gte->getIndex();
                $tmpar["expanded"] = $gte->getExpanded();
                $tmpar["leaf"] = $gte->getLeaf();
                $tmpar["pRead"] = true;
                $tmpar["pCreate"] = true;
                $tmpar["pUpdate"] = true;
                $tmpar["pDelete"] = true;

                if ($gte->getParent()) {
                    $tmpar["parentId"] = $gte->getParent()->getGroupTreeElementId();
                } else {
                    $tmpar["parentId"] = 0;
                }

                $ar[$gte->getGroupTreeElementId()] = $tmpar;

                /** @var $child GroupTreeElement */
                foreach ($gte->getChildren() as $child) {
                    if ($child->getLeaf() === true) {
                        $tmpChildAr = array();
                        $tmpChildAr["id"] = $child->getGroupTreeElementId();
                        $tmpChildAr["parentId"] = $child->getParent()->getGroupTreeElementId();
                        $tmpChildAr["name"] = $child->getName();
                        $tmpChildAr["index"] = $child->getIndex();
                        $tmpChildAr["expanded"] = $child->getExpanded();
                        $tmpChildAr["leaf"] = $child->getLeaf();

                        $tmpChildAr["pRead"] = true;
                        $tmpChildAr["pCreate"] = true;
                        $tmpChildAr["pUpdate"] = true;
                        $tmpChildAr["pDelete"] = true;
                        $ar[$child->getGroupTreeElementId()] = $tmpChildAr;
                    }
                }
            }
        } else {
            $ar = array();

            /** @var User $user */
            $user = $this->userRepository->load($userId);

            /** @var UserGroup $group */
            foreach ($user->getGroups() as $group) {
                /** @var $acl Acl */
                foreach ($group->getAcls() as $acl) {
                    if ($acl->getRead()) {
                        /** @var $gte GroupTreeElement */
                        $gte = $acl->getGroupTreeElement();

                        $tmpar = array();
                        $tmpar["id"] = $gte->getGroupTreeElementId();
                        $tmpar["isRoot"] = $gte->getRoot();
                        $tmpar["name"] = $gte->getName();
                        $tmpar["index"] = $gte->getIndex();
                        $tmpar["expanded"] = $gte->getExpanded();
                        $tmpar["leaf"] = $gte->getLeaf();
                        $tmpar["pRead"] = $acl->getRead();
                        $tmpar["pCreate"] = $acl->getCreate();
                        $tmpar["pUpdate"] = $acl->getUpdate();
                        $tmpar["pDelete"] = $acl->getDelete();

                        if ($gte->getParent()) {
                            $tmpar["parentId"] = $gte->getParent()->getGroupTreeElementId();
                        } else {
                            $tmpar["parentId"] = 0;
                        }

                        $ar[$gte->getGroupTreeElementId()] = $tmpar;

                        /** @var $child GroupTreeElement */
                        foreach ($gte->getChildren() as $child) {
                            if ($child->getLeaf() === true) {
                                $tmpChildAr = array();
                                $tmpChildAr["id"] = $child->getGroupTreeElementId();
                                $tmpChildAr["parentId"] = $child->getParent()->getGroupTreeElementId();
                                $tmpChildAr["name"] = $child->getName();
                                $tmpChildAr["index"] = $child->getIndex();
                                $tmpChildAr["expanded"] = $child->getExpanded();
                                $tmpChildAr["leaf"] = $child->getLeaf();

                                $tmpChildAr["pRead"] = $acl->getRead();
                                $tmpChildAr["pCreate"] = $acl->getCreate();
                                $tmpChildAr["pUpdate"] = $acl->getUpdate();
                                $tmpChildAr["pDelete"] = $acl->getDelete();
                                $ar[$child->getGroupTreeElementId()] = $tmpChildAr;
                            }
                        }
                    }
                }
            }
        }
        return $ar;
    }

    /**
     * updates a user entity
     *
     * @param Person $person the person value object
     *
     * @return void
     * @throws \Exception
     */
    public function updateUser(Person $person): void
    {
        /** @var User $user */
        $user = $this->userRepository->load($person->getUserId());

        if ($user->getDirectory()->getType() === Directory::DEFAULT_INTERNAL_DIRECTORY_NAME) {
            $this->updateLocalUser($user, $person);
        } else {
            $this->updateRemoteUser($user, $person);
        }

        $this->userRepository->update($user);
    }

    /**
     * deletes a user
     *
     * @param integer $userId the internal user id
     *
     * @return void
     * @throws \Exception
     */
    public function deleteUser(int $userId): void
    {
        /** @var User $user */
        $user = $this->userRepository->load($userId);

        // delete user from work queue if exists - non strict mode (throw no exception if not in queue)
        $this->workQueueService->deleteUserFromWorkQueue($user->getUserId(), false);

        foreach ($user->getIntermediateKeys() as $ik) {
            $this->intermediateKeyRepository->remove($ik);
        };

        foreach ($user->getGroups() as $group) {
            $user->removeFromGroup($group);
        }
        $this->userRepository->remove($user);
    }

    /**
     * Return all groups where user is member of
     *
     * @param integer $userId the user id
     *
     * @return array
     * @throws \Exception
     */
    public function getUsersGroups(int $userId): array
    {
        // a new unsaved user has no id - we return a empty array
        if ($userId === 0) {
            return array();
        }

        /** @var User $user */
        $user = $this->userRepository->load($userId);

        $result = array();
        /** @var UserGroup $group */
        foreach ($user->getGroups() as $group) {
            $ar = array();
            $ar['userId'] = $userId;
            $ar['groupId'] = $group->getUserGroupId();
            $ar['groupName'] = $group->getName();

            $result[] = $ar;
        }

        return $result;
    }

    /**
     * Returns all groups where user is not member of
     *
     * @param integer $userId the user id
     *
     * @return array
     */
    public function getGroupsNotInUser(int $userId): array
    {
        $result = array();
        $groups = $this->userRepository->getAvailableGroupsForUser($userId);

        /** @var UserGroup $group */
        foreach ($groups as $group) {
            $ar = array();
            $ar['userId'] = $userId;
            $ar['groupId'] = $group->getUserGroupId();
            $ar['groupName'] = $group->getName();

            $result[] = $ar;
        }

        return $result;
    }

    /**
     * checks if user is local (local authentication, no external adapter (e.g. ldap)
     *
     * @param integer $userId the user id
     *
     * @return boolean
     * @throws \Exception
     */
    public function isLocalUser(int $userId): bool
    {
        $user = $this->userRepository->load($userId);

        $directoryType = $user->getDirectory()->getType();

        if ($directoryType === "internal") {
            return true;
        } else {
            return false;
        }
    }

    /**
     * updates password of a local user
     *
     * @param Person $person the person value object
     * @param int       $userId the user id
     *
     * @return bool
     * @throws \Exception
     */
    public function changePassword(Person $person, int $userId): bool
    {
        if (!$this->isLocalUser($userId)) {
            throw new \Exception("Only local users can change their password");
        }

        /** @var User $user */
        $user = $this->userRepository->load($userId);

        if (!$user->checkPassword($person)) {
            throw new InvalidOldPasswordException("Current password is invalid");
        }

        if ($person->getNewPassword() !== $person->getRepeatedNewPassword()) {
            throw new InvalidRepeatPasswordException("repeated password is not equal");
        }

        if (!$this->validatePassword($person->getNewPassword())) {
            throw new InvalidNewPasswordException("new password doesn't complies with requirements");
        }

        if (!$user->setPassword($person->getNewPassword())) {
            throw new InvalidNewPasswordException("new password doesn't complies with requirements");
        }

        $this->userRepository->update($user);

        return true;
    }

    /**
     * Change the users theme
     *
     * @param int    $userId the user id
     * @param string $theme  the theme identifier, like "teampass" or "teampasspink"
     *
     * @return void
     * @throws \Exception
     */
    public function changeTheme(int $userId, string $theme): void
    {
        /** @var User $user */
        $user = $this->userRepository->load($userId);

        if ($theme !== Keys::DEFAULT_THEME && $theme !== Keys::PINK_THEME) {
            throw new \Exception("invalid theme name '{$theme}'");
        }

        $user->setTheme($theme);

        $this->userRepository->update($user);
    }

    /**
     * Returns the users theme
     *
     * @param integer $userId the user id
     *
     * @return string
     * @throws \Exception
     */
    public function getTheme(int $userId): string
    {
        /** @var User $user */
        $user = $this->userRepository->load($userId);

        $theme = $user->getTheme();

        if ($theme === null) {
            $theme = Keys::DEFAULT_THEME;
        }
        return $theme;
    }

    /**
     * Change the users language
     *
     * @param int    $userId   the user id
     * @param string $langCode the language code, like "en" or "de"
     *
     * @return void
     * @throws \Exception
     */
    public function changeLanguage(int $userId, string $langCode): void
    {
        /** @var User $user */
        $user = $this->userRepository->load($userId);

        $user->setLanguage($langCode);

        $this->userRepository->update($user);
    }

    /**
     * Change the users language
     *
     * @param int $userId the user id
     * @param bool $alphabeticalOrder flag if tree should be sorted alphabetically
     *
     * @return void
     * @throws \Exception
     */
    public function changeTreeAlphabeticalOrder(int $userId, bool $alphabeticalOrder): void
    {
        /** @var User $user */
        $user = $this->userRepository->load($userId);

        $user->setAlphabeticalOrder($alphabeticalOrder);

        $this->userRepository->update($user);
    }

    /**
     * Returns flag tree should be sorted alphabetically
     *
     * @param int $userId the user id
     *
     * @return bool
     * @throws \Exception
     */
    public function getTreeAlphabeticalOrder(int $userId): bool
    {
        /** @var User $user */
        $user = $this->userRepository->load($userId);

        return $user->isAlphabeticalOrder();
    }

    /**
     * Returns the users language
     *
     * @param int $userId the user id
     *
     * @return string
     * @throws \Exception
     */
    public function getLanguage(int $userId): string
    {
        /** @var User $user */
        $user = $this->userRepository->load($userId);

        $lang = $user->getLanguage();

        if (is_null($lang)) {
            $lang = Keys::DEFAULT_LANGUAGE;
        }
        return $lang;
    }

    /**
     * checks if user is admin
     *
     * @param int $userId the user-id to check
     *
     * @return bool
     * @throws \Exception
     */
    public function isAdmin(int $userId): bool
    {
        return $this->aclRepository->isAdmin($userId, false);
    }

    /**
     * Returns the amount of users
     *
     * @param bool $onlyComplete flag
     *
     * @return int
     * @throws NonUniqueResultException
     */
    public function getUserCount(bool $onlyComplete): int
    {
        return $this->userRepository->getTotalAmountOfUsers($onlyComplete);
    }

    /**
     * checks if password complexity is enabled and password matches regular expression
     *
     * @param string $password the user password
     *
     * @return bool
     * @throws \Exception
     */
    protected function validatePassword(string $password): bool
    {
        if ($this->settingService->get("directory.internal.forcePasswordComplexity")) {
            if (preg_match($this->settingService->get("directory.internal.passwordRegularExpression"), $password)) {
                return true;
            } else {
                return false;
            }
        }
        return true;
    }

    /**
     * updates a local user
     *
     * @param User      $user   the user object
     * @param Person $person the person value object
     *
     * @return void
     * @throws \Exception
     */
    protected function updateLocalUser(User $user, Person $person)
    {

        if ($person->getNewPassword()) {
            if (!$this->validatePassword($person->getNewPassword())) {
                throw new RequestValidationException("Password not matches the requirements");
            }
            $user->setPassword($person->getNewPassword());
        }

        if ($person->isEnabled()) {
            if ($user->getPassword() === null) {
                throw new RequestValidationException("User cannot be activated without valid password");
            }

            $user->enable();
        }

        if ($person->isEnabled() === false) {
            $user->disable();
        }

        if ($person->getUsername()) {
            $user->setUsername($person->getUsername());
        }

        if ($person->getFullName()) {
            $user->setFullName($person->getFullName());
        }

        if ($person->getEmailAddress()) {
            $user->setEmail($person->getEmailAddress());
        }
    }

    /**
     * updates a remote user
     *
     * @param User      $user   the user entity
     * @param Person $person the person value object
     *
     * @return void
     */
    protected function updateRemoteUser(User $user, Person $person): void
    {
        if ($person->isEnabled() === null) {
            return;
        }

        if ($person->isEnabled()) {
            $user->enable();
        } else {
            $user->disable();
        }
    }

    /**
     * loads a user entity by id
     *
     * @param integer $id a user id
     *
     * @return object
     *
     * @throws \Exception
     */
    public function get(int $id): object
    {
        return $this->userRepository->load($id);
    }
}
