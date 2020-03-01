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
use TeamPass\Core\Domain\Dto\TreeNode;
use TeamPass\ApiV1\Service\AclService;
use TeamPass\ApiV1\Service\GroupTreeService;
use TeamPass\Core\Domain\Dto\TreeNodeCollection;
use TeamPass\Core\Property\TypeConverter\TreeNodeCollectionTypeConverter;

/**
 * Class GroupTreeController
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

class GroupTreeController extends ProtectedController
{
    /**
     * @Flow\Inject
     * @var AclService
     */
    protected $aclService;

    /**
     * @Flow\Inject
     * @var GroupTreeService
     */
    protected $groupTreeService;

    /**
     * Generate the group tree
     *
     * @return void
     * @throws \Exception
     */
    public function getTreeAction(): void
    {
        $userId = (int) $this->session->getData("userId");
        $result = $this->groupTreeService->getNodesAsTree($userId);

        $this->view->assign('value', $result);
    }

    /**
     * initialize create action
     *
     * @return void
     * @throws \Neos\Flow\Mvc\Exception\NoSuchArgumentException
     */
    protected function initializeCreateAction(): void
    {
        $configuration = $this->arguments->getArgument('treeNodeCollection')->getPropertyMappingConfiguration();
        $configuration->allowAllProperties();
        $configuration->setTypeConverter(new TreeNodeCollectionTypeConverter());
    }

    /**
     * Creates a new node
     *
     * @param TreeNodeCollection $treeNodeCollection
     *
     * @return void
     * @throws \Exception
     */
    public function createAction(TreeNodeCollection $treeNodeCollection): void
    {
        $result = array();
        $userId = (int) $this->session->getData("userId");

        /** @var TreeNode $treeNode */
        foreach ($treeNodeCollection as $treeNode) {
            $this->aclService->checkPermissions($userId, "groupTree", "create", $treeNode->getParentId());
            $result = $this->groupTreeService->createNode($userId, $treeNode);
        }

        $this->response->setStatusCode(201);
        $this->view->assign('value', ['success' => true, 'children' => $result]);
    }

    /**
     * initialize updateTree action
     *
     * @return void
     * @throws \Neos\Flow\Mvc\Exception\NoSuchArgumentException
     */
    public function initializeUpdateTreeAction(): void
    {
        $configuration = $this->arguments->getArgument('treeNodeCollection')->getPropertyMappingConfiguration();
        $configuration->allowAllProperties();
        $configuration->setTypeConverter(new TreeNodeCollectionTypeConverter());
    }

    /**
     * Update one or more nodes
     *
     * @param TreeNodeCollection $treeNodeCollection
     *
     * @return void
     * @throws \Exception
     */
    public function updateTreeAction(TreeNodeCollection $treeNodeCollection): void
    {
        $userId = (int) $this->session->getData("userId");

        $this->nodeUpdate($userId, $treeNodeCollection);

        $this->view->assign('value', ['success' => true]);
    }

    /**
     * initialize delete action
     *
     * @return void
     *
     * @throws \Neos\Flow\Mvc\Exception\NoSuchArgumentException
     */
    public function initializeDeleteAction(): void
    {
        $configuration = $this->arguments->getArgument('treeNodeCollection')->getPropertyMappingConfiguration();
        $configuration->allowAllProperties();
        $configuration->setTypeConverter(new TreeNodeCollectionTypeConverter());
    }

    /**
     * Deletes a node
     *
     * @param TreeNodeCollection $treeNodeCollection
     *
     * @return void
     * @throws \Exception
     */
    public function deleteAction(TreeNodeCollection $treeNodeCollection): void
    {
        $userId = (int) $this->session->getData("userId");

        /** @var TreeNode $node */
        foreach ($treeNodeCollection as $node) {
            $this->aclService->checkPermissions($userId, "groupTree", "delete", $node->getId());
            $this->groupTreeService->deleteNode($node->getId());
        }

        $this->view->assign('value', ['success' => true]);
    }

    /**
     * calls update method for every node in array
     *
     * @param integer $userId the user id
     * @param TreeNodeCollection   $nodes  the nodes as array
     *
     * @return void
     * @throws \Exception
     */
    protected function nodeUpdate(int $userId, TreeNodeCollection $nodes): void
    {
        foreach ($nodes as $node) {
            $this->groupTreeService->updateNode($node, $userId);
        }
    }
}
