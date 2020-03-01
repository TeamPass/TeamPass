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
use TeamPass\ApiV1\Service\WorkQueueService;

/**
 * Class WorkQueueController
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

class WorkQueueController extends ProtectedAdminController
{
    /**
     * @Flow\Inject
     * @var WorkQueueService
     */
    protected $workQueueService;

    /**
     * returns all work queue elements
     *
     * @return void
     */
    public function readAction(): void
    {
        $result = $this->workQueueService->getWorkAsGrid();
        $this->view->assign('value', $result);
    }

    /**
     * deletes a element from work queue
     *
     * @param int $workQueueId the work id to delete
     *
     * @return void
     * @throws \Exception
     */
    public function deleteWorkQueueAction(int $workQueueId): void
    {
        $this->workQueueService->deleteWorkQueueElement($workQueueId);

        $this->view->assign('value', ['success' => true]);
    }
}
