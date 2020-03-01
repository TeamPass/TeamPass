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
 * Class Directory
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 *
 * @Flow\Entity
 */
class Directory
{
    /**
     * @var string
     */
    public const DEFAULT_INTERNAL_DIRECTORY_NAME = "internal";

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @var integer
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    protected $configuration;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var integer
     */
    protected $positionIndex;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $type;

    /**
     * @ORM\OneToMany(mappedBy="directory")
     * @var Collection<User>
     */
    protected $users;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $implementationClass;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    /**
     * Return ID
     *
     * @return int
     */
    public function getDirectoryId(): int
    {
        return $this->id;
    }

    /**
     * Sets the displayed directory name
     *
     * @param string $name the directory name
     *
     * @return $this
     */
    public function setDirectoryName(string $name): Directory
    {
        $this->name = $name;
        return $this;
    }

    /**
     * get directory name
     *
     * @return string
     */
    public function getDirectoryName(): string
    {
        return $this->name;
    }

    /**
     * Sets the directory configuration
     *
     * @param array $config the directory configuration as array
     *
     * @return $this
     */
    public function setConfiguration(array $config): Directory
    {
        $this->configuration = json_encode($config);
        return $this;
    }

    /**
     * returns the directory config
     *
     * @return array
     * @throws \Exception
     */
    public function getConfiguration(): array
    {
        if (is_null($this->configuration)) {
            $result = [];
        } else {
            $result = json_decode($this->configuration, true);
        }

        if (!is_array($result)) {
            throw new \Exception("error while decoding configuration of directory with id {$this->id}");
        }

        return $result;
    }

    /**
     * set directory position
     *
     * @param integer $index the position index
     *
     * @return $this
     */
    public function setPositionIndex(int $index): Directory
    {
        $this->positionIndex = $index;
        return $this;
    }

    /**
     * get directory position index
     *
     * @return int
     */
    public function getPositionIndex(): int
    {
        return $this->positionIndex;
    }

    /**
     * set directory type e.g. internal or external
     *
     * @param string $type the directory type
     *
     * @return $this
     */
    public function setType(string $type): Directory
    {
        $this->type = $type;
        return $this;
    }

    /**
     * get directory type
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * set Adapter Implementation class
     * only mandatory if type is "external"
     *
     * @param string $implementation the implementation class e.g. "LdapAdapter"
     *
     * @return $this
     */
    public function setImplementationClass(string $implementation): Directory
    {
        $this->implementationClass = $implementation;
        return $this;
    }

    /**
     * get implementation class
     *
     * @return string
     */
    public function getImplementationClass(): string
    {
        return $this->implementationClass;
    }

    /**
     * returns collection with all users coming from this Directory
     *
     * @return Collection
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }
}
