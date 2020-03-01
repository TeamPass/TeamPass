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

namespace TeamPass\ApiV1\Controller\Admin\Directory\Preview;

use Neos\Flow\Annotations as Flow;
use TeamPass\ApiV1\Controller\ProtectedAdminController;
use TeamPass\ApiV1\Service\DirectoryService;
use TeamPass\Core\Domain\Dto\Backend;
use TeamPass\Core\Domain\Dto\BackendConfiguration;
use TeamPass\Core\Property\TypeConverter\TeamPassDtoConverter;

/**
 * Class TestController
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

class TestController extends ProtectedAdminController
{
    /**
     * @Flow\Inject
     * @var DirectoryService
     */
    protected $directoryService;

    /**
     * initialize createAction method
     *
     * @return void
     * @throws \Neos\Flow\Mvc\Exception\NoSuchArgumentException
     */
    protected function initializeTestConnectionAction(): void
    {
        $backend = $this->arguments->getArgument('backend')->getPropertyMappingConfiguration();
        $backend->allowProperties('name', 'directoryId', 'implementationClass');
        $backend->setTypeConverter(new TeamPassDtoConverter());

        $beConfig = $this->arguments->getArgument('backendConfiguration')->getPropertyMappingConfiguration();
        $beConfig->allowAllProperties();
        $beConfig->setTypeConverter(new TeamPassDtoConverter());
    }

    /**
     * tests a backend directory connection
     *
     * @param Backend $backend the backend dto
     * @param BackendConfiguration $backendConfiguration the backend config dto
     *
     * @return void
     */
    public function testConnectionAction(Backend $backend, BackendConfiguration $backendConfiguration): void
    {
        try {
            $data = $this->directoryService->evalConnection($backend, $backendConfiguration);
            $status = "success";
            $statusText = "STATUS: SUCCESS\n\n";
        } catch (\Exception $e) {
            $status = "failure";
            $statusText = "STATUS: FAIL\n\n" . $e->getMessage();
            $data = array();
        }

        $result = array(
            "status" => $status,
            "statusText" => $statusText,
            "data" => $data
        );

        $this->view->assign('value', $result);
    }
}
