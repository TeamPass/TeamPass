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
use TeamPass\ApiV1\Service\WorkQueueService;
use TeamPass\Core\Domain\Dto\Group;
use TeamPass\Core\Domain\Dto\Person;
use TeamPass\ApiV1\Service\UserGroupService;
use TeamPass\ApiV1\Service\UserService;

/**
 * Class UserController
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

class UserController extends ProtectedAdminController
{
    /**
     * @Flow\Inject
     * @var UserService
     */
    protected $userService;

    /**
     * @Flow\Inject
     * @var UserGroupService
     */
    protected $userGroupService;

    /**
     * @Flow\Inject
     * @var WorkQueueService
     */
    protected $workQueueService;

    /**
     * Returns all application users
     *
     * @return void
     */
    public function getAction(): void
    {
        $result = $this->userService->getAllUsersAsGrid();
        $this->view->assign('value', $result);
    }

    /**
     * initialize the createAction method
     *
     * @return void
     * @throws \Neos\Flow\Mvc\Exception\NoSuchArgumentException
     */
    protected function initializeCreateAction(): void
    {
        $this->abstractInitialize(
            'person',
            [
                'fullName',
                'emailAddress',
                'userName',
                'newPassword',
                'enabled',
                'userId'
            ]
        );
    }

    /**
     * Creates a new user
     *
     * @Flow\ValidationGroups({"AdminUserControllerCreateAction"})
     *
     * @param Person $person the person dto
     *
     * @return void
     * @throws \Exception
     */
    public function createAction(Person $person): void
    {
        $result = $this->userService->create($person);

        $this->view->assign('value', $result);
    }

    /**
     * @return void
     * @throws \Neos\Flow\Mvc\Exception\NoSuchArgumentException
     */
    protected function initializeUpdateAction(): void
    {
        $this->abstractInitialize(
            'person',
            [
                'userId',
                'fullName',
                'emailAddress',
                'userName',
                'newPassword',
                'enabled'
            ]
        );
    }

    /**
     * Updates a user
     *
     * @Flow\ValidationGroups({"AdminUserControllerUpdateAction"})
     *
     * @param Person $person the person dto
     *
     * @return void
     * @throws \Exception
     */
    public function updateAction(Person $person): void
    {
        $this->userService->updateUser($person);

        $this->view->assign('value', []);
    }

    /**
     * Deletes a local user
     *
     * @param int $userId the user id
     *
     * @return void
     * @throws \Exception
     */
    public function deleteAction(int $userId): void
    {
        $this->userService->deleteUser($userId);

        $this->view->assign('value', []);
    }

    /**
     * returns all groups which not containing given user
     *
     * @param int $userId the user id
     *
     * @return void
     * @throws \Exception
     */
    public function readAvailableGroupsAction(int $userId): void
    {
        // if user id if 0 the store should be flushed by a empty result
        if ($userId === 0) {
            $result = [];
        } else {
            $result = $this->userService->getGroupsNotInUser($userId);
        }
        $this->view->assign('value', $result);
    }

    /**
     * @return void
     * @throws \Neos\Flow\Mvc\Exception\NoSuchArgumentException
     */
    protected function initializeUpdateUsersGroupsAction(): void
    {
        $this->abstractInitialize('group', ['groupId']);
    }

    /**
     * updates group memberships for user
     *
     * @param int $userId the user id
     * @param Group $group the group dto
     *
     * @return void
     * @throws \Exception
     */
    public function updateUsersGroupsAction(int $userId, Group $group): void
    {
        $this->userGroupService->updateUserInGroup($group->getGroupId(), $userId);

        $this->view->assign('value', []);
    }

    /**
     * @return void
     * @throws \Neos\Flow\Mvc\Exception\NoSuchArgumentException
     */
    protected function initializeDeleteUserFromGroupsAction(): void
    {
        $this->abstractInitialize('group', ['groupId']);
    }

    /**
     * delete given user from given groups
     *
     * @param int $userId the user id
     * @param Group $group the group dto
     *
     * @return void
     * @throws \Exception
     */
    public function deleteUserFromGroupsAction(int $userId, Group $group): void
    {
        $this->userGroupService->deleteUserFromGroup($group->getGroupId(), $userId);

        $this->view->assign('value', []);
    }

    /**
     * get all group which containing given user
     *
     * @param int $userId the user id
     *
     * @return void
     * @throws \Exception
     */
    public function readUsersGroupsAction(int $userId): void
    {
        // if user id if 0 the store should be flushed by a empty result
        if ($userId === 0) {
            $result = [];
        } else {
            $result = $this->userService->getUsersGroups($userId);
        }

        $this->view->assign('value', $result);
    }

    /**
     * Adds user to work queue
     *
     * @param int $userId the user id
     *
     * @return void
     * @throws \Exception
     */
    public function addUserToWorkQueueAction(int $userId): void
    {
        $this->workQueueService->addToWorkQueue($userId);

        $result = [
            'success' => true,
            'result' => $this->translatorService->trans('ADMIN.ADD_USER_TO_WORK_QUEUE_SUCCESS_MSG'),
        ];

        $this->view->assign('value', $result);
    }
}
