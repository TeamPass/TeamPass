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
use TeamPass\Core\Domain\Dto\Permission;
use TeamPass\Core\Domain\Model\Acl;
use TeamPass\Core\Domain\Model\GroupTreeElement;
use TeamPass\Core\Domain\Model\UserGroup;
use TeamPass\Core\Domain\Model\GroupElement;
use TeamPass\Core\Domain\Repository\AclRepository;
use TeamPass\Core\Exception\InsufficientPermissionException;

/**
 * Class AclService
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

class AclService extends AbstractService
{
    /**
     * @Flow\Inject
     * @var EncryptionService
     */
    protected $encryptionService;

    /**
     * Returns all UserGroups not currently used by this group tree element
     *
     * @param integer $gteId the requested groupTreeElementId
     *
     * @return array
     * @throws \Exception
     */
    public function readAvailableUserGroupsForGroup(int $gteId): array
    {
        //FIXME: replace method wir repository query
        $currentGroups = array();
        $allGroups = array();

        /** @var GroupTreeElement $groupTree */
        $groupTree = $this->groupTreeElementRepository->load($gteId);

        foreach ($groupTree->getAcls() as $acl) {
            $ar = array();

            /** @var Acl $acl */
            $ar['userGroupId'] = $acl->getGroup()->getUserGroupId();
            $ar['groupName'] = $acl->getGroup()->getName();
            $ar['gteId'] = $gteId;

            $currentGroups[$ar['userGroupId']] = $ar;
        }

        $userGroups = $this->userGroupRepository->findByAdmin(false);

        /** @var UserGroup $userGroup */
        foreach ($userGroups as $userGroup) {
            $ar = array();
            $ar['userGroupId'] = $userGroup->getUserGroupId();
            $ar['groupName'] = $userGroup->getName();
            $ar['gteId'] = $gteId;

            $allGroups[$ar['userGroupId']] = $ar;
        }

        $diff = array_values(array_diff_key($allGroups, $currentGroups));

        return $diff;
    }

    /**
     * creates a new ACL with no permissions for given user group
     *
     * @param Permission $permission the permission dto
     *
     * @return array
     * @throws \Exception
     */
    public function create(Permission $permission): array
    {
        /** @var GroupTreeElement $gte */
        $gte = $this->groupTreeElementRepository->load($permission->getGteId());

        /** @var UserGroup $userGroup */
        $userGroup = $this->userGroupRepository->load($permission->getUserGroupId());

        // check if an acl for usergroup and grouptreelement already exists
        $this->aclRepository->failIfExists($userGroup->getUserGroupId(), $gte->getGroupTreeElementId());

        $acl = new Acl();
        $acl->setGroup($userGroup)
            ->setGroupTreeElement($gte)
            ->setRead(false)
            ->setCreate(false)
            ->setUpdate(false)
            ->setDelete(false);

        $this->aclRepository->add($acl);
        $this->persistenceManager->persistAll();

        $result = array (
            "groupName" => $acl->getGroup()->getName(),
            "inherited" => $acl->getInherited(),
            "pRead" => $acl->getRead(),
            "pCreate" => $acl->getCreate(),
            "pUpdate" => $acl->getUpdate(),
            "pDelete" => $acl->getDelete(),
            "id" => $acl->getAclId()
        );

        return $result;
    }

    /**
     * deletes a acl based on given id
     *
     * @param integer $aclId the acl id
     *
     * @return bool
     * @throws \Exception
     */
    public function deletePermission(int $aclId): bool
    {
        /** @var Acl $acl */
        $acl = $this->aclRepository->load($aclId);

        $this->encryptionService->removeIntermediateKeysOnACLChangeByUserGroup($acl->getGroup()->getUserGroupId());

        $this->aclRepository->remove($acl);

        return true;
    }

    /**
     * Updates acl permissions based on given id an values
     *
     * @param Permission $permission the permission dto
     *
     * @return array
     * @throws \Exception
     */
    public function updatePermission(Permission $permission): array
    {
        /** @var Acl $acl */
        $acl = $this->aclRepository->load($permission->getId());

        // define the response array
        $result = array();

        $currentRead = $acl->getRead();

        $acl->setRead($permission->isPRead());
        $acl->setCreate($permission->isPCreate());
        $acl->setUpdate($permission->isPUpdate());
        $acl->setDelete($permission->isPDelete());

        if ($acl->getInherited() === true) {
            $acl->setInherited(false);
            $result['inherited'] = false;
        }

        $this->aclRepository->update($acl);

        if ($currentRead !== $acl->getRead()) {
            if ($acl->getRead() === true) {
                $this->encryptionService->updateIntermediateKeysForGte($acl->getGroupTreeElement());
            } else {
                $this->encryptionService->removeIntermediateKeysOnACLChangeByUserGroup(
                    $acl->getGroup()->getUserGroupId()
                );
            }
        }

        return $result;
    }

    /**
     * delegate to sub method based on controller name
     *
     * @param integer $userId     the user id
     * @param string  $controller controller name as string
     * @param string  $action     permission action
     * @param integer $identifier entity id
     *
     * @return void
     * @throws \Exception
     */
    public function checkPermissions(int $userId, string $controller, string $action, int $identifier): void
    {
        switch ($controller) {
            case "groupTree":
                $this->checkGroupTreePermission($userId, $action, $identifier);
                break;
            case "groupElement":
                $this->checkGroupElementPermission($userId, $action, $identifier);
                break;
            default:
                throw new \Exception("invalid permission to check");
        }
    }

    /**
     * check if user has permission "$action" (e.g. "create") on given group element
     *
     * @param int    $userId     user id
     * @param string $action     permission action
     * @param int    $identifier entity id
     *
     * @return void
     * @throws \Exception
     */
    protected function checkGroupElementPermission(int $userId, string $action, int $identifier): void
    {
        /** @var GroupElement $groupElement */
        $groupElement = $this->groupElementRepository->load($identifier);

        /** @var GroupTreeElement $groupTreeElement */
        $groupTreeElement = $groupElement->getGroupTreeElement();

        if (!$groupTreeElement instanceof GroupTreeElement) {
            throw new \Exception("GroupTreeElement not found");
        }

        if ($groupTreeElement->getLeaf() === true) {
            $id = $groupTreeElement->getParent()->getGroupTreeElementId();
        } else {
            $id = $groupTreeElement->getGroupTreeElementId();
        }

        /** @var AclRepository $aclRepository */
        $aclRepository = $this->aclRepository->checkPermission($userId, $action, $id);
    }

    /**
     * check if user has permission "$action" (e.g. "create") on given group tree element
     *
     * @param int    $userId     user id
     * @param string $action     permission action
     * @param int    $identifier entity id
     *
     * @return void
     * @throws \Exception
     */
    protected function checkGroupTreePermission(int $userId, string $action, int $identifier): void
    {
        /** @var GroupTreeElement $groupTreeElement */
        $groupTreeElement = $this->groupTreeElementRepository->load($identifier);

        if ($groupTreeElement->getLeaf() === true) {
            $id = $groupTreeElement->getParent()->getGroupTreeElementId();
        } else {
            $id = $groupTreeElement->getGroupTreeElementId();
        };

        /** @var AclRepository $aclRepository */
        $aclRepository = $this->aclRepository->checkPermission($userId, $action, $id);
    }

    /**
     * returns admin state of user
     *
     * @param integer $userId the user id
     * @param bool    $strict strict flag
     *
     * @return mixed
     * @throws InsufficientPermissionException
     */
    public function isAdmin(int $userId, bool $strict = true): bool
    {
        return $this->aclRepository->isAdmin($userId, $strict);
    }

    /**
     * returns the effective permissions of a group tree element from users view
     *
     * @param GroupTreeElement $entity the entity
     * @param integer          $userId user's id
     *
     * @return array
     * @throws InsufficientPermissionException
     */
    public function getUserPermissionForGroupTreeElement(GroupTreeElement $entity, int $userId): array
    {
        if ($this->isAdmin($userId, false)) {
            // set default permissions
            $result = array();
            $result['pRead'] = true;
            $result['pUpdate'] = true;
            $result['pCreate'] = true;
            $result['pDelete'] = true;

            return $result;
        }

        // leaf entities cannot have permissions. use the parent entity (must not leaf) instead
        if ($entity->getLeaf()) {
            // override entity by parent entity
            $entity = $entity->getParent();
        }

        /** @var array $acls */
        $acls = $this->aclRepository->fetchUserPermissionsForGte($entity->getGroupTreeElementId(), $userId);

        // set default permissions
        $result = array();
        $result['pRead'] = false;
        $result['pUpdate'] = false;
        $result['pCreate'] = false;
        $result['pDelete'] = false;

        /** @var Acl $acl */
        foreach ($acls as $acl) {
            if ($acl->getRead()) {
                $result['pRead'] = $acl->getRead();
            }
            if ($acl->getUpdate()) {
                $result['pUpdate'] = $acl->getUpdate();
            }
            if ($acl->getCreate()) {
                $result['pCreate'] = $acl->getCreate();
            }
            if ($acl->getDelete()) {
                $result['pDelete'] = $acl->getDelete();
            }
        }

        return $result;
    }
}
