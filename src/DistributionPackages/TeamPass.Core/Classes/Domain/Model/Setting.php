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

namespace TeamPass\Core\Domain\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

/**
 * Class Setting
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 *
 * @Flow\Entity
 */
class Setting
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @var integer
     */
    protected $id;

    /**
     * @ORM\Column(type="string", unique=true, length=191)
     * @var string
     */
    protected $settingName;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $defaultValue;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $customValue;

     /**
     * returns id of this entity
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Returns the setting name
     *
     * @return string
     */
    public function getSettingName(): string
    {
        return $this->settingName;
    }

    /**
     * Returns the default value
     *
     * @return string
     */
    public function getDefaultValue(): string
    {
        return $this->defaultValue;
    }

    /**
     * Returns the custom value
     *
     * @return string|null
     */
    public function getCustomValue(): ?string
    {
        return $this->customValue;
    }

    /**
     * Sets a custom value for this setting
     *
     * @param string|null $customValue the custom value
     *
     * @return void
     */
    public function setCustomValue(?string $customValue): void
    {
        $this->customValue = $customValue;
    }

    /**
     * Returns the custom value if it's set, otherwise the default value
     *
     * @return string
     */
    public function getValue(): string
    {
        if (isset($this->customValue)) {
            return $this->customValue;
        }
        return $this->defaultValue;
    }
}
