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

namespace TeamPass\ApiV1\Service;

use Neos\Flow\Annotations as Flow;
use TeamPass\Core\Domain\Model\Setting;

/**
 * Class SettingService
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

class SettingService extends AbstractService
{
    /**
     * loads a setting entity by given setting name parameter
     *
     * @param string $name the setting name
     *
     * @return mixed|null|object
     * @throws \Exception
     */
    public function get(string $name)
    {
        /** @var Setting $entity */
        $entity = $this->settingRepository->getSettingByName($name);

        if ($entity->getValue() === 'true') {
            return true;
        }

        if ($entity->getValue() === 'false') {
            return false;
        }

        return $entity->getValue();
    }

    /**
     * Returns all settings
     *
     * @return array
     */
    public function getAllSettingsAsGrid(): array
    {
        $settings = $this->settingRepository->findAll();
        $result = array();

        /** @var  Setting $setting */
        foreach ($settings as $setting) {
            $ar = array();
            $ar['settingId'] = $setting->getId();
            $ar['settingName'] = $setting->getSettingName();
            $ar['defaultValue'] = $setting->getDefaultValue();
            $ar['customValue'] = $setting->getCustomValue();

            $result[] = $ar;
        }
        return $result;
    }

    /**
     * updates the custom value for given setting
     *
     * @param int    $id          the id
     * @param string $customValue the custom value
     *
     * @return void
     * @throws \Exception
     */
    public function update(int $id, string $customValue): void
    {
        /** @var Setting $setting */
        $setting = $this->settingRepository->findByIdentifier($id);

        if ($customValue === '') {
            $customValue = null;
        }

        $setting->setCustomValue($customValue);

        $this->settingRepository->update($setting);
    }
}
