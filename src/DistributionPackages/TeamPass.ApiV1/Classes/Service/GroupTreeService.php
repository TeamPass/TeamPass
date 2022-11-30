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
use Neos\Flow\Persistence\Exception\IllegalObjectTypeException;
use TeamPass\Core\Domain\Model\GroupTreeElement;
use TeamPass\Core\Domain\Model\User;
use TeamPass\Core\Domain\Model\Acl;
use TeamPass\Core\Domain\Dto\TreeNode;
use TeamPass\Core\Exception\InvalidRequestException;

/**
 * Class UserGroupService
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

class GroupTreeService extends AbstractService
{
    /**
     * Holds Ids and indexes to find find entity with same index
     *
     * @var array
     */
    protected $cache = array();

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
     * @Flow\Inject
     * @var TranslatorService
     */
    protected $translatorService;

    const MIGRATION_BASE_PATH = "Migration";

    /**
     * creates a new node
     *
     * @param int      $userId the user id
     * @param TreeNode $node   the node
     *
     * @return array
     * @throws \Exception
     */
    public function createNode(int $userId, TreeNode $node): array
    {
        $parent = $this->groupTreeElementRepository->load($node->getParentId());

        $entity = new GroupTreeElement();
        $entity->setName($node->getText())
            ->setParent($parent)
            ->setIndex($node->getIndex())
            ->setExpanded($node->isExpanded())
            ->setLeaf($node->isLeaf());

        // we need the entity id
        $this->groupTreeElementRepository->add($entity);
        $this->persistenceManager->persistAll();

        // only if node is not leaf (not a endpoint) copy parent's permissions
        if (!$entity->getLeaf()) {
            $this->copyPermissions($parent, $entity);
        }

        $permissions = $this->aclService->getUserPermissionForGroupTreeElement($entity, $userId);

        $result = array(
            "id" => $entity->getGroupTreeElementId(),
            "recordid" => $entity->getGroupTreeElementId(),
            "children" => array()
        );

        return array_merge($result, $permissions);
    }


    /**
     * deletes given node
     *
     * @param integer $gteId the group tree element id
     *
     * @return void
     * @throws \Exception
     */
    public function deleteNode(int $gteId): void
    {
        $gte = $this->groupTreeElementRepository->load($gteId);

        if ($gte->getLeaf() === false) {
            $this->deleteNonLeafGroupTreeElement($gte);
        } else {
            $this->deleteLeafGroupTreeElement($gte);
        }
    }

    /**
     * @param int $groupId
     *
     * @return array
     * @throws \Exception
     */
    public function getGroupNamePath(int $groupId)
    {
        /** @var $group GroupTreeElement  */
        $group = $this->groupTreeElementRepository->load($groupId);

        $path = $this->getParentsName($group);
        $path[] = $group->getName();

        return $path;
    }


    protected function getParentsName(GroupTreeElement $group, $result=[])
    {
        if (is_null($group->getParent())) {
            $result[] = self::MIGRATION_BASE_PATH;
            return array_reverse($result);
        }

        $parentGroup = $group->getParent();
        $result[] = $parentGroup->getName();

        return $this->getParentsName($parentGroup, $result);
    }

    /**
     * try's to delete a leaf group tree element. this element has to be empty
     *
     * @param GroupTreeElement $gte the group tree element to delete
     *
     * @return void
     * @throws InvalidRequestException
     * @throws IllegalObjectTypeException
     */
    protected function deleteLeafGroupTreeElement(GroupTreeElement $gte): void
    {
        if ($gte->getElements()->count() > 0) {
            throw new InvalidRequestException(
                $this->translatorService->trans('SERVICE.GTE_WITH_GE_DELETION_NOT_ALLOWED')
            );
        }
        $this->groupTreeElementRepository->remove($gte);
    }

    /**
     * try's to delete a non leaf group tree element
     *
     * @param GroupTreeElement $gte the group tree element to delete
     *
     * @return void
     * @throws InvalidRequestException
     * @throws IllegalObjectTypeException
     */
    protected function deleteNonLeafGroupTreeElement(GroupTreeElement $gte): void
    {
        if ($gte->getChildren()->count() > 0) {
            throw new InvalidRequestException(
                $this->translatorService->trans('SERVICE.GTE_WITH_GTE_DELETION_NOT_ALLOWED')
            );
        }

        foreach ($gte->getAcls() as $acl) {
            $this->aclRepository->remove($acl);
        }

        $this->groupTreeElementRepository->remove($gte);
    }

    /**
     * updates given node
     *
     * @param TreeNode $node   node parameters
     * @param integer     $userId the userId
     *
     * @return void
     * @throws \Exception
     */
    public function updateNode(TreeNode $node, int $userId): void
    {
        /** @var GroupTreeElement $entity */
        $entity = $this->groupTreeElementRepository->load($node->getId());

        // checks if user expended or collapsed a tree element
        if ($this->onlyExpandedUpdate($entity, $node, $userId)) {
            $this->updateExpandedState($node, $userId);

            $this->groupTreeElementRepository->update($entity);

            return;
        }

        // checks if user changed position of a tree element in the same hierarchy. Only admin are allowed to change
        // the position
        if ($this->onlyPositionChange($node)) {
            if ($this->userService->isAdmin($userId)) {
                $this->updatePosition($entity, $node);
            } else {
                return;
            }
        }

        if ($node->getParentId() === null) {
            $this->updateGroupTreeElement($entity, $userId, $node);
        } else {
            /** @var GroupTreeElement $parent */
            $parent = $this->groupTreeElementRepository->findByIdentifier($node->getParentId());

            if ($entity->getParent() !== $parent) {
                if ($entity->getLeaf() === true) {
                    $this->moveLeafGroupTreeElement($entity, $parent, $userId, $node);
                } else {
                    if ($parent === null) {
                        throw new InvalidRequestException(
                            $this->translatorService->trans('SERVICE.MOVING_ELEMENT_TO_ROOT_NOT_ALLOWED')
                        );
                    }

                    $this->moveNonLeafGroupTreeElement($entity, $parent, $userId, $node);
                }
            }
        }

        $this->groupTreeElementRepository->update($entity);
    }


    /**
     * called when a leaf group tree element should be updated
     *
     * @param GroupTreeElement $entity the current group tree element to update
     * @param integer          $userId the user id which would update this entity
     * @param TreeNode      $node   the group tree node with eventually updated values
     *
     * @return void
     * @throws \Exception
     */
    protected function updateGroupTreeElement(GroupTreeElement $entity, int $userId, TreeNode $node): void
    {
        // check if user is allowed to update entity
        $this->aclService->checkPermissions($userId, "groupTree", "update", $entity->getGroupTreeElementId());

        if ($node->getText() !== null) {
            $entity->setName($node->getText());
        }

        if ($node->getIndex() !== null) {
            $entity->setIndex($node->getIndex());
        }
    }

    /**
     * called when moving a leaf group tree element to an other parent
     *
     * @param GroupTreeElement $entity the current group tree element to update
     * @param GroupTreeElement $parent the parent group tree element based on given parent id
     * @param integer          $userId the user id which would update this entity
     * @param TreeNode      $node   the group tree node with eventually updated values
     *
     * @return void
     * @throws \Exception
     */
    protected function moveLeafGroupTreeElement(
        GroupTreeElement $entity,
        GroupTreeElement $parent,
        int $userId,
        TreeNode $node
    ): void {
        // check if user is allowed to delete
        $this->aclService->checkPermissions($userId, "groupTree", "delete", $entity->getGroupTreeElementId());
        // check if user is allowed to create new entries under new parent
        $this->aclService->checkPermissions($userId, "groupTree", "create", $parent->getGroupTreeElementId());

        $entity->setParent($parent)->setIndex($node->getIndex());

        $this->groupTreeElementRepository->update($entity);

        $this->encryptionService->updateIntermediateKeysForGte($entity);
    }

    /**
     * called when moving group tree element to an other parent
     *
     * @param GroupTreeElement $entity the current group tree element to update
     * @param GroupTreeElement $parent the parent group tree element based on given parent id
     * @param integer          $userId the user id which would update this entity
     * @param TreeNode      $node   the group tree node with eventually updated values
     *
     * @return void
     * @throws \Exception
     */
    protected function moveNonLeafGroupTreeElement(
        GroupTreeElement $entity,
        GroupTreeElement $parent,
        int $userId,
        TreeNode $node
    ): void {
        // check if user is allowed to delete entries on this level
        $this->aclService->checkPermissions($userId, "groupTree", "delete", $entity->getGroupTreeElementId());
        // check if user is allowed to create new entries under new parent
        $this->aclService->checkPermissions($userId, "groupTree", "create", $parent->getGroupTreeElementId());

        /** @var GroupTreeElement $child */
        foreach ($entity->getChildren() as $child) {
            if ($child->getLeaf() === false) {
                throw new \Exception(
                    "GroupTreeElement with id {$entity->getGroupTreeElementId()} has a non-leaf child"
                );
            }
        }

        // all users having read permission on given group tree element
        /** @var array $users */
        $users = $this->encryptionService->getAllUsersWithReadPermissionOnGroupTreeElement($entity);

        /** @var Acl $acl */
        foreach ($entity->getAcls() as $acl) {
            // delete all inherited acls for this non-leaf group tree element
            if ($acl->getInherited() === true) {
                $entity->getAcls()->removeElement($acl);
                $this->aclRepository->remove($acl);
            }
        }

        $entity->setParent($parent)->setIndex($node->getIndex());
        $this->groupTreeElementRepository->update($entity);

        // copy permission from future parent entity to non-leaf entity
        $this->copyPermissions($parent, $entity);

        // creates new iks for new users and deletes old iks former allowed users
        $this->encryptionService->updateIntermediateKeysForGte($entity);
    }

    /**
     * checks if request is only a position index change
     *
     * @param TreeNode $node a node record
     *
     * @return bool
     */
    protected function onlyPositionChange(TreeNode $node): bool
    {
        if ($node->getParentId() !== null || $node->getText() !== null || $node->isExpanded() !== null) {
            return false;
        }

        if ($node->getIndex() !== null) {
            return true;
        }

        return false;
    }

    /**
     * validate if "update" action updates only expanded state - in this case the user does not need additional
     * permissions for this action
     *
     * @param GroupTreeElement $entity the group tree element which should be updated
     * @param TreeNode      $node   the updated values coming from the web app
     * @param int              $userId the user id, which making this request
     *
     * @return bool
     * @throws \Exception
     */
    protected function onlyExpandedUpdate(GroupTreeElement $entity, TreeNode $node, int $userId)
    {
        // check if node name have been changed
        if ($node->getText() !== null && $entity->getName() !== $node->getText()) {
            return false;
        }

        // check if parent id have been changed
        if ($node->getParentId() !== null) {
            return false;
        }

        if ($this->aclService->isAdmin($userId, false)) {
            if ($node->getIndex() !== null && $entity->getIndex() !== $node->getIndex()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns nodes as tree
     *
     * @param int $id the user id
     *
     * @return array
     * @throws \Exception
     */
    public function getNodesAsTree(int $id): array
    {
        $ar = array();

        // load user entity, to store tree settings
        /** @var User $user */
        $user = $this->userRepository->load($id);

        $settings = $user->getTreeSettings();
        $alphabeticalOrder = $user->isAlphabeticalOrder();

        $list = $this->userService->findAllGroupTreeElementsByPermission($id);

        // fetch all non leaf group tree elements. we use this elements to build a complete tree even if user hasn't
        // read permissions on this group tree elements
        /** @scrutinizer ignore-call */
        $allNonLeafGroupTreeElements = $this->groupTreeElementRepository->findByLeaf(false);

        $gtes = array();

        /** @var GroupTreeElement $gte */
        foreach ($allNonLeafGroupTreeElements as $gte) {
            $gtes[$gte->getGroupTreeElementId()] = $gte;
        }

        $list = $this->normalizeTree($list, $gtes);


        foreach ($list as $row) {
            $expanded = false;

            // inject custom settings
            if (isset($settings["expanded"][$row['id']]) && $settings["expanded"][$row['id']] === true) {
                $expanded = true;
            }

            if ($this->idIndexInCache($row['parentId'], $row['index'])) {
                $row['index'] = $this->getNextFreeIndexForId($row['parentId']);
            }

            if (!isset($row['isRoot'])) {
                $row['isRoot'] = null;
            } else {
                // if node isRoot (the only root node) force expanded to true to avoid a sync bug in extjs frontend
                $expanded = true;
            }

            $ar[$row['parentId']][$row['index']] = array(
                    "text" => $row['name'],
                    "parentId" => $row['parentId'],
                    "index" => $row['index'],
                    "id" => $row['id'],
                    "expanded" => $expanded,
                    "leaf" => $row['leaf'],
                    "isRoot" => $row['isRoot'],
                    "pRead" => $row['pRead'],
                    "pCreate" => $row['pCreate'],
                    "pUpdate" => $row['pUpdate'],
                    "pDelete" => $row['pDelete']
                );

            $this->storeIdIndexCache($row['parentId'], $row['index']);
        }

        // array is empty when user have no permission
        if (empty($ar)) {
            $res = [];
        } else {
            // re order indices
            foreach ($ar as $key => $row) {
                ksort($row);
                $ar[$key] = array_values($row);
            }
            $res = $this->recursiveTreeBuild($ar, null, $alphabeticalOrder);
        }

        $res = $this->addRootNode($res);

        return $res;
    }

    /**
     * checks if a valid tree can be built. fills up all non existing gtes (e.g user has no permission on this gte)
     * with gtes coming vom $gte array
     *
     * @param array $ar   all group tree elements user has min. read permissions
     * @param array $gtes all non leaf group tree elements
     *
     * @return array
     */
    protected function normalizeTree(array $ar, array $gtes): array
    {
        // create a copy of array with users gte's
        $list = $ar;
        foreach ($ar as $row) {
            if ($row["leaf"] == true) {
                continue;
            }

            $parentId = $row['parentId'];
            $rootFound = false;

            while ($rootFound == false) {
                $hit = false;

                foreach ($list as $tmpRow) {
                    if ($tmpRow["id"] == $parentId) {
                        $parentId = $tmpRow["parentId"];
                        $hit = true;
                        break;
                    }
                }

                if ($hit === false) {
                    if ($parentId === 0) {
                        break;
                    }

                    $gte = $gtes[$parentId];

                    $node = array();
                    $node["id"] = $gte->getGroupTreeElementId();
                    $node["isRoot"] = $gte->getRoot();
                    $node["name"] = $gte->getName();
                    $node["index"] = $gte->getIndex();
                    $node["expanded"] = $gte->getExpanded();
                    $node["leaf"] = $gte->getLeaf();
                    $node["pRead"] = false;
                    $node["pCreate"] = false;
                    $node["pUpdate"] = false;
                    $node["pDelete"] = false;

                    if ($gte->getRoot() == 1) {
                        $node["parentId"] = 0;
                        $list[$node["id"]] = $node;
                        $rootFound = true;
                        // break;
                    } else {
                        $node["parentId"] = $gte->getParent()->getGroupTreeElementId();
                        $list[$node["id"]] = $node;
                        $parentId = $node["parentId"];
                    }
                }
            }
        }

        return $list;
    }

    /**
     * recursive tree build
     *
     * @param array      $ar                node array
     * @param array|null $curAr             node array
     * @param bool       $alphabeticalOrder flag if tree should be sorted alphabetically
     *
     * @return array
     */
    protected function recursiveTreeBuild(array $ar, ?array $curAr, bool $alphabeticalOrder): array
    {
        $i = 0;
        $result = array();

        if ($curAr === null) {
            $curAr = $ar[0];
        }

        foreach ($curAr as $key => $row) {
            if ($row["leaf"] != 1) {
                $row["leaf"] = false;
            } else {
                $row["leaf"] = true;
            }
            if ($row["parentId"] === 0) {
                $row["parentId"] = "root";
            }
            $row["index"] = $key;
            $row["recordid"] = $row['id'];
            $result[$i] = $row;

            $result[$i]['children'] = array();

            if (isset($row['id']) && isset($ar[$row['id']])) {
                if (is_array($ar[$row['id']])) {
                    $result[$i]['children'] = $this->recursiveTreeBuild($ar, $ar[$row['id']], $alphabeticalOrder);
                }
            }
            $i++;
        }

        if ($alphabeticalOrder) {
            // sort by name
            usort($result, function ($a, $b) {
                return strcasecmp($a["text"][0], $b["text"][0]);
            });
            // reindex array
            $result = array_values($result);

            foreach ($result as $key => &$row) {
                $row['index'] = $key;
            }
        }

        return $result;
    }

    /**
     * Adds a root node after building node tree
     *
     * @param array $res node tree
     *
     * @return array
     */
    protected function addRootNode(array $res): array
    {
        return array(
            "index" => 0,
            "leaf" => false,
            "expanded" => true,
            'id' => 'root',
            "parentId" => null,
            "text" => "Root",
            "children" => $res
        );
    }

    /**
     * return next free index number
     *
     * @param int $id index id
     *
     * @return mixed
     */
    protected function getNextFreeIndexForId(int $id)
    {
        $value = max(array_keys($this->cache[$id]));
        return ++$value;
    }

    /**
     * Returns true if id is already in cache, else false
     *
     * @param int $id    group tree element id
     * @param int $index position index id
     *
     * @return bool
     */
    protected function idIndexInCache(int $id, int $index): bool
    {
        if (isset($this->cache[$id][$index])) {
            return true;
        }
        return false;
    }

    /**
     * save id in cache to prevent multiple allocation
     *
     * @param int $id    the group tree element id
     * @param int $index the position index
     *
     * @return void
     */
    protected function storeIdIndexCache(int $id, int $index): void
    {
        $this->cache[$id][$index] = true;
    }

    /**
     * updates expanded state for given user and node
     *
     * @param TreeNode $node  the node as array
     * @param int         $userId the user id
     *
     * @return void
     * @throws \Exception
     */
    public function updateExpandedState(TreeNode $node, int $userId): void
    {
        $groupTreeElementId = $node->getId();
        $expanded = $node->isExpanded();

        /** @var User $user */
        $user = $this->userRepository->load($userId);

        $settings = $user->getTreeSettings();

        if (isset($settings["expanded"][$groupTreeElementId])) {
            if ($settings["expanded"][$groupTreeElementId] !== $expanded) {
                $settings["expanded"][$groupTreeElementId] = $expanded;
            }
        } else {
            $settings["expanded"][$groupTreeElementId] = $expanded;
        }

        $user->setTreeSettings($settings);

        $this->userRepository->update($user);
    }

    /**
     * updates the position index of a GroupTreeElement entity
     *
     * @param GroupTreeElement $groupTreeElement the entity
     * @param TreeNode      $node             the node data to update
     *
     * @return void
     * @throws \Exception
     */
    protected function updatePosition(GroupTreeElement $groupTreeElement, TreeNode $node): void
    {
        $positionIndex = $node->getIndex();

        $groupTreeElement->setIndex($positionIndex);

        $this->groupTreeElementRepository->update($groupTreeElement);
    }

    /**
     * copy permission from source to target element
     *
     * @param GroupTreeElement $sourceGroupTreeElement a group tree element
     * @param GroupTreeElement $targetGroupTreeElement a group tree element
     *
     * @return void
     * @throws \Exception
     */
    protected function copyPermissions(
        GroupTreeElement $sourceGroupTreeElement,
        GroupTreeElement $targetGroupTreeElement
    ): void {
        $acls = $this->aclRepository->fetchPermissions($sourceGroupTreeElement);

        /** @var Acl $acl */
        foreach ($acls as $acl) {
            $newAcl = new Acl();
            $newAcl->setGroup($acl->getGroup())
                ->setGroupTreeElement($targetGroupTreeElement)
                ->setRead($acl->getRead())
                ->setCreate($acl->getCreate())
                ->setUpdate($acl->getUpdate())
                ->setDelete($acl->getDelete())
                ->setInherited(true);
            $this->aclRepository->add($newAcl);

            $targetGroupTreeElement->getAcls()->add($newAcl);
        }
    }
}
