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
use TeamPass\Core\Domain\Dto\Backend;
use TeamPass\Core\Domain\Dto\BackendConfiguration;
use TeamPass\Core\Exception\ParameterValidationException;
use TeamPass\ApiV1\Service\DirectoryService;
use TeamPass\Core\Property\TypeConverter\TeamPassDtoConverter;

/**
 * Class DirectoryController
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

class DirectoryController extends ProtectedAdminController
{
    /**
     * @Flow\Inject
     * @var DirectoryService
     */
    protected $directoryService;

    /**
     * Reads all configured directories
     *
     * @return void
     * @throws \Exception
     */
    public function getAllDirectoriesAction(): void
    {
        $result = $this->directoryService->getAllDirectoriesAsGrid();

        $this->view->assign('value', $result);
    }

    /**
     * Returns one directory based on given id
     *
     * @param int $directoryId a directory id
     *
     * @return void
     * @throws \Exception
     */
    public function getDirectoryAction(int $directoryId): void
    {
        $result = $this->directoryService->getDirectory($directoryId);

        $this->view->assign('value', $result);
    }

    /**
     * Returns all external directories
     *
     * @return void
     */
    public function getExternalDirectoryAction(): void
    {
        $result = $this->directoryService->getExternalDirectoriesAsGrid();

        $this->view->assign('value', $result);
    }

    /**
     * initialize createAction method
     *
     * @return void
     * @throws \Neos\Flow\Mvc\Exception\NoSuchArgumentException
     */
    protected function initializeCreateAction(): void
    {
        $backend = $this->arguments->getArgument('backend')->getPropertyMappingConfiguration();
        $backend->allowProperties('name', 'directoryId', 'implementationClass');
        $backend->setTypeConverter(new TeamPassDtoConverter());

        $beConfig = $this->arguments->getArgument('backendConfiguration')->getPropertyMappingConfiguration();
        $beConfig->allowAllProperties();
        $beConfig->setTypeConverter(new TeamPassDtoConverter());
    }

    /**
     * Creates a new external directory
     *
     * @param Backend $backend the backend dto
     * @param BackendConfiguration $backendConfiguration
     *
     * @return void
     * @throws \Exception
     */
    public function createAction(Backend $backend, BackendConfiguration $backendConfiguration): void
    {
        $backend->setType("external");

        $result = $this->directoryService->createDirectory($backend, $backendConfiguration);

        $this->response->setStatusCode(201);

        $this->view->assign('value', $result);
    }

    /**
     * initialize updateAction method
     *
     * @return void
     * @throws \Neos\Flow\Mvc\Exception\NoSuchArgumentException
     */
    protected function initializeUpdateAction(): void
    {
        $backend = $this->arguments->getArgument('backend')->getPropertyMappingConfiguration();
        $backend->allowProperties('name', 'directoryId', 'implementationClass');
        $backend->setTypeConverter(new TeamPassDtoConverter());

        $beConfig = $this->arguments->getArgument('backendConfiguration')->getPropertyMappingConfiguration();
        $beConfig->allowAllProperties();
        $beConfig->setTypeConverter(new TeamPassDtoConverter());
    }

    /**
     * Updates a existing directory
     *
     * @param Backend $backend the backend dto
     * @param BackendConfiguration $backendConfiguration
     *
     * @Flow\ValidationGroups({"DirectoryControllerUpdateAction"})
     * @return void
     * @throws \Exception
     */
    public function updateAction(Backend $backend, BackendConfiguration $backendConfiguration): void
    {
        $result = $this->directoryService->updateDirectory($backend, $backendConfiguration);

        $this->view->assign('value', $result);
    }

    /**
     * deletes a directory
     *
     * @param int $directoryId the directory id to delete
     *
     * @return void
     * @throws \Exception
     */
    public function deleteAction(int $directoryId): void
    {
        try {
            $result = $this->directoryService->deleteDirectory($directoryId);

            $this->view->assign('value', $result);
        } catch (\Exception $e) {
            $this->response->setStatusCode(403);
            $this->view->assign('value', ['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * change directory position index
     *
     * @param int $directoryId the directory id
     * @param string $direction the direction ("up" or "down")
     *
     * @return void
     * @throws ParameterValidationException
     * @throws \Exception
     */
    public function changeIndexDirectoryAction(int $directoryId, string $direction): void
    {
        if ($direction === "up") {
            $this->directoryService->changeIndexUp($directoryId);
        } elseif ($direction === "down") {
            $this->directoryService->changeIndexDown($directoryId);
        } else {
            throw new ParameterValidationException(
                "'{$directoryId}' is a invalid 'direction' parameter: '{$direction}'"
            );
        }

        $this->view->assign('value', ['success' => true]);
    }

    /**
     * sync backend directory
     *
     * @param int $directoryId the directory id
     *
     * @return void
     * @throws \Exception
     */
    public function syncAction(int $directoryId): void
    {
        try {
            $this->directoryService->sync($directoryId);

            $this->view->assign('value', ['success' => true]);
        } catch (\Exception $e) {
            $this->response->setStatusCode(403);
            $this->view->assign('value', ['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
