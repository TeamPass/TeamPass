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

use Doctrine\Common\Collections\ArrayCollection;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Property\TypeConverter\CollectionConverter;
use TeamPass\ApiV1\Service\AdminService;
use TeamPass\ApiV1\Service\DirectoryService;
use TeamPass\ApiV1\Service\EncryptionService;
use TeamPass\ApiV1\Service\GroupTreeService;
use TeamPass\ApiV1\Service\TranslatorService;
use TeamPass\ApiV1\Service\UserGroupService;
use TeamPass\ApiV1\Service\UserService;
use TeamPass\ApiV1\Service\WorkQueueService;
use TeamPass\Core\Domain\Dto\DummyCollection;
use TeamPass\Core\Domain\Dto\MassEncryption;

/**
 * Class AdminController
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

class AdminController extends ProtectedAdminController
{
    /**
     * @Flow\Inject
     * @var AdminService
     */
    protected $adminService;

    /**
     * @Flow\Inject
     * @var GroupTreeService
     */
    protected $groupTreeService;

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
     * @var DirectoryService
     */
    protected $directoryService;

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
     * @var TranslatorService
     */
    protected $translatorService;

    /**
     * Returns a list containing all administrative backend modules
     *
     * @return void
     */
    public function getTreeAction(): void
    {
        $children = array(
            array(
                "text" => $this->translatorService->trans('ADMIN.TREE_PERMISSIONS'),
                "alias" => "widget.adminpermissionscontainer",
                "leaf" => true,
                "parentId" => "root"
            ),
            array(
                "text" => $this->translatorService->trans('ADMIN.TREE_USERS'),
                "alias" => "widget.adminusercontainer",
                "leaf" => true,
                "parentId" => "root"
            ),
            array(
                "text" => $this->translatorService->trans('ADMIN.TREE_GROUPS'),
                "alias" => "widget.admingroupcontainer",
                "leaf" => true,
                "parentId" => "root"
            ),
            array(
                "text" => $this->translatorService->trans('ADMIN.TREE_DIRECTORIES'),
                "alias" => "widget.admindirectorycontainer",
                "leaf" => true,
                "parentId" => "root"
            ),
            array(
                "text" => $this->translatorService->trans('ADMIN.TREE_SETTINGS'),
                "alias" => "widget.adminsettingscontainer",
                "leaf" => true,
                "parentId" => "root"
            ),
            array(
                "text" => $this->translatorService->trans('ADMIN.TREE_EXPORT'),
                "alias" => "widget.adminexportcontainer",
                "leaf" => true,
                "parentId" => "root"
            )
        );

        $this->view->assign(
            'value',
            $children
        );
    }

    /**
     *
     * @return void
     * @throws \Neos\Flow\Mvc\Exception\NoSuchArgumentException
     */
    public function initializeEncryptForUserAction()
    {
        $this->abstractInitialize('massEncryption', []);
    }

    /**
     * encrypt elements for user
     *
     * @param MassEncryption $massEncryption;
     *
     * @Flow\ValidationGroups({"AdminControllerEncryptForUserAction"})
     *
     * @return void
     * @throws \Exception
     */
    public function encryptForUserAction(MassEncryption $massEncryption): void
    {
        $adminUserId = (int) $this->session->getData("userId");

        // if client sends aes keys to encrypt for user
        if ($massEncryption->getEntries()) {
            $this->encryptionService->saveAesKeys($massEncryption->getEntries(), $adminUserId);
        }

        $value = $this->encryptionService->getBatchedAesKeys($adminUserId, $massEncryption->getUserId());

        if ($value === false) {
            $response["finished"] = true;
        } else {
            $content = $this->aesEncrypt($this->encode($value));

            $response["finished"] = false;
            $response["content"] = $content;
        }

        $basicResponse = array("success" => true);

        $this->view->assign(
            'value',
            array_merge($basicResponse, $response)
        );
    }

    /**
     * encryption poll for admin users
     *
     * @return void
     * @throws \Exception
     */
    public function doEncryptionQueuePollAction(): void
    {
        $userId = (int) $this->session->getData("userId");

        $result = $this->workQueueService->getWorkCount($userId);

        $this->view->assign('value', ['success' => true, 'result' => $result]);
    }
}
