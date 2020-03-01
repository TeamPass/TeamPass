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

namespace TeamPass\Core\Domain\Dto;

use Neos\Flow\Annotations as Flow;

/**
 * Class AppSetting
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */
class AppSetting
{
    /**
     * @Flow\Validate(type="NotEmpty")
     * @var int
     */
    protected $settingId;

    /**
     * @var string
     */
    protected $customValue;

    /**
     * @return int
     */
    public function getSettingId(): int
    {
        return $this->settingId;
    }

    /**
     * @param int $settingId
     *
     * @return void
     */
    public function setSettingId(int $settingId): void
    {
        $this->settingId = $settingId;
    }

    /**
     * @return string
     */
    public function getCustomValue(): string
    {
        return $this->customValue;
    }

    /**
     * @param string $customValue
     *
     * @return void
     */
    public function setCustomValue(string $customValue): void
    {
        $this->customValue = $customValue;
    }
}
