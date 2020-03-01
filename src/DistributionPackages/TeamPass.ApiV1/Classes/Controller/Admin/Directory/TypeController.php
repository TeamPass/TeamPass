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

namespace TeamPass\ApiV1\Controller\Admin\Directory;

use Neos\Flow\Annotations as Flow;
use TeamPass\ApiV1\Controller\ProtectedAdminController;
use TeamPass\ApiV1\Service\DirectoryService;

/**
 * Class TypeController - handles the directory type requests
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

class TypeController extends ProtectedAdminController
{
    /**
     * @Flow\Inject
     * @var DirectoryService
     */
    protected $directoryService;

    /**
     * Reads all available directory types (OpenLDAP, ActiveDirectory)
     *
     * @return void
     * @throws \Exception
     */
    public function getTypesAction(): void
    {
        $result = $this->directoryService->getAllDirectoriesAsGrid();

        $this->view->assign('value', $result);
    }
}
