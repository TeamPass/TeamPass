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
use TeamPass\ApiV1\Service\SettingService;
use TeamPass\Core\Domain\Dto\AppSetting;

/**
 * Class SettingController
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

class SettingController extends ProtectedAdminController
{
    /**
     * @Flow\Inject
     * @var SettingService
     */
    protected $settingsService;

    /**
     * Returns all application settings
     *
     * @return void
     */
    public function getSettingsAction(): void
    {
        $result = $this->settingsService->getAllSettingsAsGrid();
        $this->view->assign('value', $result);
    }

    /**
     * initialize startHandshake action
     *
     * @return void
     * @throws \Neos\Flow\Mvc\Exception\NoSuchArgumentException
     */
    protected function initializeUpdateAction(): void
    {
        $this->abstractInitialize('appSetting', ['settingId', 'customValue']);
    }

    /**
     * Updates a Setting
     *
     * @param AppSetting $appSetting
     *
     * @return void
     * @throws \Exception
     */
    public function updateAction(AppSetting $appSetting): void
    {
        $this->settingsService->update($appSetting->getSettingId(), $appSetting->getCustomValue());

        $this->view->assign('value', ['success' => true]);
    }
}
