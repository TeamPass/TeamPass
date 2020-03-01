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

namespace TeamPass\Core\Domain\Repository;

use Neos\Flow\Annotations as Flow;
use TeamPass\Core\Domain\Model\Setting;

/**
 * Class SettingRepository
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 *
 * @Flow\Scope("singleton")
 */
class SettingRepository extends AbstractRepository
{
    /**
     * returns a setting model by given settingName
     *
     * @param string $settingName
     *
     * @return Setting
     * @throws \Exception
     */
    public function getSettingByName(string $settingName): Setting
    {
        $query = $this->createQuery();
        $result = $query->matching($query->equals('settingName', $settingName))->execute()->getFirst();

        if (!$result instanceof Setting) {
            throw new \Exception("No entity found for key '{$settingName}'");
        }
        return $result;
    }
}
