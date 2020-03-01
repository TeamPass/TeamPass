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
use TeamPass\ApiV1\Service\AdminService;
use TeamPass\Core\Domain\Dto\Permission;

/**
 * Class PermissionController
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

class PermissionController extends ProtectedAdminController
{
    /**
     * @Flow\Inject
     * @var AdminService
     */
    protected $adminService;

    /**
     * Generate group tree
     *
     * @return void
     */
    public function getGroupTreeAction(): void
    {
        $result = $this->adminService->getNodesAsTree();
        $this->view->assign('value', $result);
    }

    /**
     * returns current permissions for given group tree element id
     *
     * @param int|null $gteId the group tree element id
     *
     * @return void
     * @throws \Exception
     */
    public function readAction(?int $gteId = null): void
    {
        if ($gteId === null) {
            $result = [];
        } else {
            $result = $this->adminService->getPermissionsForGroupTreeElement($gteId);
        }
        $this->view->assign('value', $result);
    }

    /**
     * initialice the createAction method
     *
     * @return void
     * @throws \Neos\Flow\Mvc\Exception\NoSuchArgumentException
     */
    protected function initializeCreateAction(): void
    {
        $this->abstractInitialize(
            'permission',
            [
                'gteId',
                'userGroupId',
                'id',
                'pCreate',
                'pDelete',
                'pRead',
                'pUpdate'
            ]
        );
    }

    /**
     * creates a new acl
     *
     * @param Permission $permission the permission dto
     *
     * @Flow\ValidationGroups({"AdminPermissionControllerCreateAction"})
     *
     * @return void
     * @throws \Exception
     */
    public function createAction(Permission $permission): void
    {
        $result = $this->aclService->create($permission);

        $this->view->assign('value', $result);
    }

    /**
     * initialize startHandshake action
     *
     * @return void
     * @throws \Neos\Flow\Mvc\Exception\NoSuchArgumentException
     */
    protected function initializeUpdateAction(): void
    {
        $this->abstractInitialize(
            'permission',
            [
                'id',
                'gteId',
                'userGroupId',
                'pCreate',
                'pDelete',
                'pRead',
                'pUpdate'
            ]
        );
    }

    /**
     * updates the permissions of a existing acl
     *
     * @param Permission $permission the permission dto
     *
     * @Flow\ValidationGroups({"AdminPermissionControllerUpdateAction"})
     *
     * @return void
     * @throws \Exception
     */
    public function updateAction(Permission $permission): void
    {
        $result = $this->aclService->updatePermission($permission);

        $this->view->assign('value', $result);
    }

    /**
     * initialize startHandshake action
     *
     * @return void
     * @throws \Neos\Flow\Mvc\Exception\NoSuchArgumentException
     */
    protected function initializeDeleteAction(): void
    {
        $this->abstractInitialize(
            'permission',
            [
                'pRead',
                'pCreate',
                'pUpdate',
                'pDelete',
                'gteId',
                'userGroupId',
                'id'
            ]
        );
    }

    /**
     * Delete a permission
     *
     * @param Permission $permission the permission dto
     *
     * @Flow\ValidationGroups({"AdminPermissionControllerDeleteAction"})
     *
     * @return void
     * @throws \Exception
     */
    public function deleteAction(Permission $permission): void
    {
        $this->aclService->deletePermission($permission->getId());

        $this->view->assign('value', []);
    }

    /**
     * initialize startHandshake action
     *
     * @return void
     * @throws \Neos\Flow\Mvc\Exception\NoSuchArgumentException
     */
    protected function initializeReadAvailableUserGroupsForGroup2Action(): void
    {
        $this->abstractInitialize('permission', ['groupId']);
    }

    /**
     * Returns all user groups that are not currently used by this group tree element
     *
     * @param int|null $gteId the group tree element id
     *
     * @return void
     * @throws \Exception
     */
    public function readAvailableUserGroupsForGroupAction(?int $gteId = null): void
    {
        if ($gteId === null) {
            $result = [];
        } else {
            $result = $this->aclService->readAvailableUserGroupsForGroup($gteId);
        }
        $this->view->assign('value', $result);
    }
}
