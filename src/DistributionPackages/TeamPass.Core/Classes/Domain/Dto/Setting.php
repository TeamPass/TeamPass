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
 * Class Setting
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */
class Setting
{
    /**
     * @var string
     * @Flow\Validate(type="NotEmpty", validationGroups={"ManagementControllerChangeLanguageAction"})
     */
    protected $language;

    /**
     * @var string
     * @Flow\Validate(type="NotEmpty", validationGroups={"ManagementControllerChangeThemeAction"})
     */
    protected $theme;

    /**
     * @var bool
     * @Flow\Validate(type="NotEmpty", validationGroups={"ManagementControllerSetTreeAlphabeticalOrderAction"})
     */
    protected $alphabeticalOrder;

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @param string $language
     */
    public function setLanguage(string $language): void
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getTheme(): string
    {
        return $this->theme;
    }

    /**
     * @param string $theme
     */
    public function setTheme(string $theme): void
    {
        $this->theme = $theme;
    }

    /**
     * @return bool
     */
    public function isAlphabeticalOrder(): bool
    {
        return $this->alphabeticalOrder;
    }

    /**
     * @param bool $alphabeticalOrder
     */
    public function setAlphabeticalOrder(bool $alphabeticalOrder): void
    {
        $this->alphabeticalOrder = $alphabeticalOrder;
    }
}
