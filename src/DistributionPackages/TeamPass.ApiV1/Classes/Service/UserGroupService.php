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
use TeamPass\Core\Domain\Dto\Group;
use TeamPass\Core\Domain\Model\UserGroup;
use TeamPass\Core\Domain\Model\User;
use TeamPass\Core\Exception\UserException;

/**
 * Class UserGroupService
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

class UserGroupService extends AbstractService
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
     * @var UserService
     */
    protected $userService;

    /**
     * Returns all groups as grid array
     *
     * @return array
     */
    public function getAllGroupsAsGrid(): array
    {
        $groups = $this->userGroupRepository->findAll();
        $result = array();

        /* @var UserGroup $group */
        foreach ($groups as $group) {
            $ar = array();
            $ar['groupId'] = $group->getUserGroupId();
            $ar['groupName'] = $group->getName();
            $ar['isAdmin'] = $group->getAdmin();

            $result[] = $ar;
        }
        return $result;
    }

    /**
     * Creates a group based on given parameters
     *
     * @param Group $group the group dto
     *
     * @return array
     * @throws \Exception
     */
    public function createGroup(Group $group): array
    {
        $userGroup = $this->userGroupRepository->findOneByName($group->getGroupName());

        // check if group already exists and throw exception if case
        if ($userGroup instanceof UserGroup) {
            throw new UserException("creating Group failed: Group '{$group->getGroupName()}' already exists");
        }

        $userGroup = new UserGroup();
        $userGroup->setName($group->getGroupName())
            ->setAdmin($group->isAdmin());

        $this->userGroupRepository->add($userGroup);

        $this->persistenceManager->persistAll();

        return array("groupId" => $userGroup->getUserGroupId());
    }

    /**
     * updates a user group by given parameters
     *
     * @param int    $groupId   the groupId to update
     * @param Group $groupDto the group dto
     *
     * @return bool
     * @throws \Exception
     */
    public function updateGroup(int $groupId, Group $groupDto): bool
    {
        $userGroup = $this->userGroupRepository->load($groupId);

        $groupCheck = $this->userGroupRepository->findByName($groupDto->getGroupName());

        if ($groupCheck instanceof UserGroup && $groupCheck->getName() !== $userGroup->getName()) {
            throw new UserException("update Group failed: Groupname '{$groupDto->getGroupName()}' already exists");
        }

        // we need the current admin flag to detect if group state was changed
        $currentAdminFlag = $userGroup->getAdmin();

        // set new group name
        $userGroup->setName($groupDto->getGroupName());
        // set new admin flag - perhaps it has not been changed
        $userGroup->setAdmin($groupDto->isAdmin());

        $this->userGroupRepository->update($userGroup);

        // if admin privileges are revoked
        if ($currentAdminFlag === true && $groupDto->isAdmin() === false) {
            /** @var User $user */
            foreach ($userGroup->getUsers() as $user) {
                // check for every user in group if we need to remove some intermediate keys, because
                // he's not admin anymore
                $this->encryptionService->removeIntermediateKeysOnACLChange($user->getUserId());
            }
        }

        // if group has now admin privileges
        if ($currentAdminFlag === false && $groupDto->isAdmin() === true) {
            /** @var User $user */
            foreach ($userGroup->getUsers() as $user) {
                $this->workQueueService->addToWorkQueueSilent($user->getUserid());
            }
        }

        return true;
    }

    /**
     * deletes a Group by given id
     *
     * @param int $groupId the group id
     *
     * @return void
     * @throws \Exception
     */
    public function deleteGroup(int $groupId): void
    {
        $group = $this->userGroupRepository->load($groupId);

        foreach ($group->getUsers() as $user) {
            $group->getUsers()->removeElement($user);
        }

        foreach ($group->getAcls() as $acl) {
            $this->aclRepository->remove($acl);
        }

        $this->userGroupRepository->remove($group);
    }

    /**
     * Returns all users in given group id
     *
     * @param int $groupId the group id
     *
     * @return array
     * @throws \Exception
     */
    public function getUsersInGroup(int $groupId): array
    {
        // a new unsafed group has no id - we return a empty array
        if ($groupId === 0) {
            return array();
        }

        $group = $this->userGroupRepository->load($groupId);

        $result = array();

        /** @var User $user */
        foreach ($group->getUsers() as $user) {
            $ar = array();
            $ar['userId'] = $user->getUserId();
            $ar['groupId'] = $groupId;
            $ar['displayName'] = $user->getFullName() . " (" . $user->getUserName() . ")";

            $result[] = $ar;
        }

        return $result;
    }

    /**
     * Returns all users which are not in given group id
     *
     * @param int $groupId the group id
     *
     * @return array
     * @throws \Exception
     */
    public function getUsersNotInGroup(int $groupId): array
    {
        $currentUsers = array();

        if ($groupId !== 0) {
            /* @var UserGroup $group */
            $group = $this->userGroupRepository->load($groupId);

            /** @var User $user */
            foreach ($group->getUsers() as $user) {
                $currentUsers[$user->getUserId()] = true;
            }
        }

        $allUsers = $this->userRepository->findAll();

        $result = array();
        foreach ($allUsers as $user) {
            if (!isset($currentUsers[$user->getUserId()]) || $currentUsers[$user->getUserId()] !== true) {
                $ar = array();
                $ar['userId'] = $user->getUserId();
                $ar['groupId'] = $groupId;
                $ar['displayName'] = $user->getFullName() . " (" . $user->getUserName() . ")";

                $result[] = $ar;
            }
        }

        return $result;
    }

    /**
     * adds new users to group if necessary
     *
     * @param int $groupId the group-id
     * @param int $userId  the user id to add
     *
     * @return void
     * @throws \Exception
     */
    public function updateUserInGroup(int $groupId, int $userId): void
    {
        $group = $this->userGroupRepository->load($groupId);

        // check if at least one user is created and setup is complete
        $userCount = $this->userService->getUserCount(true);

        /** @var User $user */
        $user = $this->userRepository->load($userId);

        if (!$user instanceof User) {
            throw new UserException("Cannot add user {$userId} to group {$groupId} - user does not exist");
        }

        // if user was not in group the return value is true. we need to encrypt new entries for this user,
        // but only if at least one user has a completed setup and user has now admin privileges
        if ($user->addToGroup($group) && $userCount > 0 && !$this->userService->isAdmin($userId)) {
            $this->workQueueService->addToWorkQueueSilent($user->getUserId());
        }

        $this->userRepository->update($user);
    }

    /**
     * adds new users to group if necessary
     *
     * @param string $groupName a group name
     * @param string $userName  a user name
     *
     * @return void
     * @throws \Exception
     */
    public function updateUserInGroupByNames(string $groupName, string $userName): void
    {
        /** @var UserGroup $group */
        $group = $this->userGroupRepository->findOneByName($groupName);

        if (!$group instanceof UserGroup) {
            throw new UserException("Group '{$groupName}' does not exist!");
        }

        /** @var User $user */
        $user = $this->userRepository->findOneByUsername($userName);

        if (!$user instanceof User) {
            throw new UserException("Cannot add user '{$userName}' to group '{$groupName}' - user does not exist");
        }

        // if user was not in group the return value is true. we need to encrypt new entries for this user,
        // but only if at least one user has a completed setup
        if ($user->addToGroup($group)) {
            $this->userRepository->update($user);
            $this->workQueueService->addToWorkQueueSilent($user->getUserId());
        }
    }

    /**
     * deletes one or more users from group if necessary
     *
     * @param int $groupId the group id
     * @param int $userId  the user id to delete
     *
     * @return void
     * @throws \Exception
     */
    public function deleteUserFromGroup(int $groupId, int $userId): void
    {
        /** @var UserGroup $group */
        $group = $this->userGroupRepository->load($groupId);
        $user = $this->userRepository->load($userId);
        $user->removeFromGroup($group);
        $this->userRepository->update($user);
        $this->encryptionService->updateIntermediateKeysForUser($userId);
    }
}
