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
 * Class NodeController - handles the directory type requests
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

class NodeController extends ProtectedAdminController
{
    /**
     * @Flow\Inject
     * @var DirectoryService
     */
    protected $directoryService;

    /**
     * Returns the presets for given implementation class
     *
     * @param string $implementationClass the implementation class
     *
     * @return void
     * @throws \Exception
     */
    public function getPresetForImplementationClassAction(string $implementationClass): void
    {
        $result = $this->directoryService->getDirectoryPresets($implementationClass);

        $this->view->assign('value', $result);
    }
}
