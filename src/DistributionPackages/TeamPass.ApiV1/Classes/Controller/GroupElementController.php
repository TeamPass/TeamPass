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
use TeamPass\ApiV1\Service\AclService;
use TeamPass\ApiV1\Service\GroupElementService;
use TeamPass\Core\Domain\Dto\Element;

/**
 * Class GroupElementController
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

class GroupElementController extends ProtectedController
{
    /**
     * @Flow\Inject
     * @var AclService
     */
    protected $aclService;

    /**
     * @Flow\Inject
     * @var GroupElementService
     */
    protected $groupElementService;

    /**
     * Read all elements for a group tree element
     *
     * @param int|null $groupId
     *
     * @return void
     * @throws \Exception
     */
    public function readAction(?int $groupId = null): void
    {
        // if no groupId was given, return a empty array
        if ($groupId === null) {
            $this->view->assign('value', []);
            return;
        }

        $userId = (int) $this->session->getData("userId");
        $this->aclService->checkPermissions($userId, "groupTree", "read", $groupId);
        $result = $this->groupElementService->getAllElements($userId, $groupId);

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
            'element',
            [
                'groupId',
                'title',
                'comment',
                'elementId',
                'isEncrypted',
                'template',
                'rsaEncAesKey'
            ]
        );
    }

    /**
     * creates a new element
     *
     * @Flow\ValidationGroups({"GroupElementControllerCreateAction"})
     *
     * @param Element $element
     *
     * @return void
     * @throws \Exception
     */
    public function createAction(Element $element): void
    {
        $userId = (int) $this->session->getData("userId");
        $this->aclService->checkPermissions($userId, "groupTree", "create", $element->getGroupId());

        $result = $this->groupElementService->createGroupElement($element);

        $this->response->setStatusCode(201);
        $this->view->assign('value', ['elementId' => $result]);
    }

    /**
     * initialize the updateAction method
     *
     * @return void
     * @throws \Neos\Flow\Mvc\Exception\NoSuchArgumentException
     */
    protected function initializeUpdateAction(): void
    {
        $this->abstractInitialize(
            'element',
            [
                'title',
                'comment',
                'elementId',
                'isEncrypted',
                'template',
                'rsaEncAesKey'
            ]
        );
    }

    /**
     * updates a element
     *
     * @Flow\ValidationGroups({"GroupElementControllerUpdateAction"})
     *
     * @param int     $elementId the element's id which should be updated
     * @param Element $element   the element dto
     *
     * @return void
     * @throws \Exception
     */
    public function updateAction(int $elementId, Element $element): void
    {
        $userId = (int) $this->session->getData("userId");
        $this->aclService->checkPermissions($userId, "groupElement", "update", $elementId);
        $result = $this->groupElementService->updateGroupElement($userId, $element);

        $this->view->assign('value', ['success' => true, 'result' => $result]);
    }

    /**
     * deletes a element
     *
     * @param int $elementId the element's id which should be deleted
     *
     * @return void
     * @throws \Exception
     */
    public function deleteAction(int $elementId): void
    {
        $userId = (int) $this->session->getData("userId");

        $this->aclService->checkPermissions($userId, "groupElement", "delete", $elementId);
        $this->groupElementService->deleteGroupElement($elementId);

        $this->view->assign('value', ['success' => true]);
    }
}
