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

namespace TeamPass\ApiV1\Controller\Admin;

use Neos\Flow\Annotations as Flow;
use TeamPass\ApiV1\Controller\ProtectedAdminController;
use TeamPass\ApiV1\Service\UserGroupService;
use TeamPass\Core\Domain\Dto\Group;

/**
 * Class UserGroupController
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

class UserGroupController extends ProtectedAdminController
{
    /**
     * @Flow\Inject
     * @var UserGroupService
     */
    protected $userGroupService;

    /**
     * Returns all local UserGroups
     *
     * @return void
     */
    public function getAction(): void
    {
        $result = $this->userGroupService->getAllGroupsAsGrid();
        $this->view->assign('value', $result);
    }

    /**
     * @return void
     * @throws \Neos\Flow\Mvc\Exception\NoSuchArgumentException
     */
    protected function initializeCreateAction(): void
    {
        $this->abstractInitialize('group', ['groupId', 'groupName', 'isAdmin']);
    }

    /**
     * creates a new group
     *
     * @param Group $group the user group dto
     *
     * @Flow\ValidationGroups({"AdminUserGroupControllerCreateAction"})
     *
     * @return void
     * @throws \Exception
     */
    public function createAction(Group $group): void
    {
        $result = $this->userGroupService->createGroup($group);
        $this->view->assign('value', $result);
    }

    /**
     * @return void
     * @throws \Neos\Flow\Mvc\Exception\NoSuchArgumentException
     */
    protected function initializeUpdateAction(): void
    {
        $this->abstractInitialize('group', ['groupId', 'groupName', 'isAdmin']);
    }

    /**
     * Updates a local group
     *
     * @param int $groupId the group id
     * @param Group $group the group dto
     *
     * @Flow\ValidationGroups({"AdminUserGroupControllerUpdateAction"})
     *
     * @return void
     * @throws \Exception
     */
    public function updateAction(int $groupId, Group $group): void
    {
        $this->userGroupService->updateGroup($groupId, $group);
        $this->view->assign('value', []);
    }

    /**
     * Deletes a local group
     *
     * @param int $groupId the group id
     *
     * @return void
     * @throws \Exception
     */
    public function deleteAction(int $groupId): void
    {
        $this->userGroupService->deleteGroup($groupId);
        $this->view->assign('value', []);
        //$this->view->assign('value', $result);
    }

    /**
     * read all users in group
     *
     * @param int $groupId the group id
     *
     * @return void
     * @throws \Exception
     */
    public function readUsersInGroupAction(int $groupId): void
    {
        // if group id if 0 the store should be flushed by a empty result
        if ($groupId === 0) {
            $result = [];
        } else {
            $result = $this->userGroupService->getUsersInGroup($groupId);
        }
        $this->view->assign('value', $result);
    }

    /**
     * @return void
     * @throws \Neos\Flow\Mvc\Exception\NoSuchArgumentException
     */
    protected function initializeUpdateUserInGroupAction(): void
    {
        $this->abstractInitialize('group', ['userId']);
    }

    /**
     * updates users in a group
     *
     * @param int $groupId the group id
     * @param Group $group the group dto
     *
     * @Flow\ValidationGroups({"AdminUserGroupControllerUpdateUserInGroupAction"})
     *
     * @return void
     * @throws \Exception
     */
    public function updateUserInGroupAction(int $groupId, Group $group): void
    {
        $this->userGroupService->updateUserInGroup($groupId, $group->getUserId());
        $this->view->assign('value', []);
    }

    /**
     * @return void
     * @throws \Neos\Flow\Mvc\Exception\NoSuchArgumentException
     */
    protected function initializeDeleteUserFromGroupAction(): void
    {
        $this->abstractInitialize('group', ['userId']);
    }

    /**
     * deletes a user from a group
     *
     * @param int $groupId the group id
     * @param Group $group the group dto
     *
     * @Flow\ValidationGroups({"AdminUserGroupControllerDeleteUserFromGroupAction"})
     *
     * @return void
     * @throws \Exception
     */
    public function deleteUserFromGroupAction(int $groupId, Group $group): void
    {
        $this->userGroupService->deleteUserFromGroup($groupId, $group->getUserId());
        $this->view->assign('value', []);
    }

    /**
     * returns all users which are not already in given group
     *
     * @param int $groupId the group id
     *
     * @return void
     * @throws \Exception
     */
    public function readAvailableUsersAction(int $groupId): void
    {
        // if group id if 0 the store should be flushed by a empty result
        if ($groupId === 0) {
            $result = [];
        } else {
            $result = $this->userGroupService->getUsersNotInGroup($groupId);
        }
        $this->view->assign('value', $result);
    }
}
