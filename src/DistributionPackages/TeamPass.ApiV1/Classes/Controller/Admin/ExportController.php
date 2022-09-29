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
use TeamPass\ApiV1\Service\ExportService;
use TeamPass\ApiV1\Service\GroupTreeService;
use TeamPass\Core\Domain\Dto\PrivateKey;
use TeamPass\Core\Exception\ExportException;

/**
 * Class PermissionController
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

class ExportController extends ProtectedAdminController
{
    /**
     * @Flow\Inject
     * @var GroupTreeService
     */
    protected $groupTreeService;

    /**
     * @Flow\Inject
     * @var ExportService
     */
    protected $exportService;

    /**
     * getGroupPathAction
     *
     * @param int $groupId
     * @return void
     */
    public function getGroupPathAction(int $groupId): void
    {
        $path = $this->groupTreeService->getGroupNamePath($groupId);

        $result['id'] = $groupId;
        $result['path'] = $path;

        $response = [
            'success' => true,
            'result' => $result
        ];

        $this->view->assign('value', $response);
    }

    /**
     * initialize postExport action
     *
     * @return void
     * @throws \Neos\Flow\Mvc\Exception\NoSuchArgumentException
     */
    protected function initializePostExportAction(): void
    {
        $this->abstractInitialize('privateKey', ['privateKey']);
    }

    /**
     * postExportAction
     *
     * @param int $groupId
     * @param PrivateKey $privateKey
     *
     * @return void
     */
    public function postExportAction(int $groupId,PrivateKey $privateKey): void
    {
        $start = microtime(true);
        $userId = (int) $this->session->getData("userId");

        $privateKey = $this->normalizePrivateKey($privateKey->getPrivateKey());

        $filename = $this->exportService->process($userId, $groupId, $privateKey);

        $duration = microtime(true) - $start;
        $response = [
            'success' => true,
            'result' => "SUCCESS\n\nexport Successful after {$duration} Seconds.\n\nFilename: '{$filename}'"
        ];

        $this->view->assign('value', $response);
    }

    protected function normalizePrivateKey($privKey)
    {
        $privKey = "-----BEGIN RSA PRIVATE KEY-----\n" . $privKey . "\n-----END RSA PRIVATE KEY-----\n";
        $privateKeyObj = openssl_pkey_get_private($privKey);
        if ($privateKeyObj === false) {
            throw new ExportException("damn");
        }

        return $privateKeyObj;
    }
}
