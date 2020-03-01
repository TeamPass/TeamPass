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
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

/**
 * Class ElementTemplate
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 *
 * @Flow\Entity
 */
class ElementTemplate
{
    public const DEFAULT_TEMPLATE = "DEFAULT_TEMPLATE";

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $internalName;

    /**
     * @var Collection<EncryptedContent>
     * @ORM\OneToMany(mappedBy="template")
     */
    protected $encryptedContents;

    /**
     * class constructor
     */
    public function __construct()
    {
        $this->encryptedContents = new ArrayCollection();
    }

    /**
     * returns Entity id
     *
     * @return int
     */
    public function getElementTemplateId(): int
    {
        return $this->id;
    }

    /**
     * set readable name
     *
     * @param string $name the readable name
     *
     * @return $this
     */
    public function setName(string $name): ElementTemplate
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Returns the displayed name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * sets internalName used by our WebApp to select right Template
     *
     * @param string $intName the internal name
     *
     * @return $this
     */
    public function setInternalName(string $intName): ElementTemplate
    {
        $this->internalName = $intName;
        return $this;
    }

    /**
     * Returns the internal Name
     *
     * @return string
     */
    public function getInternalName(): string
    {
        return $this->internalName;
    }
}
