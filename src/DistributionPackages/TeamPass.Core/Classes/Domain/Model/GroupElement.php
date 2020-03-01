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
 * Class GroupElement
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 *
 * @Flow\Entity
 */
class GroupElement
{
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
     * @ORM\ManyToOne(inversedBy="elements")
     * @var GroupTreeElement
     **/
    protected $groupTreeElement;

    /**
     * @var integer
     * @ORM\Column(type="integer",nullable=true)
     */
    protected $positionIndex;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $tags = "";

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $description = "";

    /**
     * @var Collection<EncryptedContent>
     * @ORM\OneToMany(mappedBy="groupElement", cascade={"remove"}, orphanRemoval=true)
     */
    protected $encryptedContents;

    /**
     * @var Collection<IntermediateKey>
     * @ORM\OneToMany(mappedBy="groupElement", cascade={"remove"}, orphanRemoval=true)
     */
    protected $intermediateKeys;

    /**
     * entity class constructor
     */
    public function __construct()
    {
        $this->intermediateKeys = new ArrayCollection();
        $this->encryptedContents = new ArrayCollection();
    }

    /**
     * Returns entity id
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * sets GroupElement name
     *
     * @param string $name group element name
     *
     * @return $this
     */
    public function setName(string $name): GroupElement
    {
        $this->name = $name;
        return $this;
    }

    /**
     * returns name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * set groupTreeElement (parent-node)
     *
     * @param GroupTreeElement $groupTreeElement group tree element instance
     *
     * @return $this
     */
    public function setGroupTreeElement(GroupTreeElement $groupTreeElement): GroupElement
    {
        $this->groupTreeElement = $groupTreeElement;
        return $this;
    }

    /**
     * returns GroupTreeElement
     *
     * @return GroupTreeElement
     */
    public function getGroupTreeElement(): GroupTreeElement
    {
        return $this->groupTreeElement;
    }

    /**
     * get all encrypted Entities
     *
     * @return ArrayCollection
     */
    public function getEncryptedContents(): Collection
    {
        return $this->encryptedContents;
    }

    /**
     * Returns element comment
     *
     * @return string
     */
    public function getComment(): string
    {
        return $this->description;
    }

    /**
     * Sets the element comment
     *
     * @param string $comment element comment
     *
     * @return $this
     */
    public function setComment(string $comment): GroupElement
    {
        $this->description = $comment;
        return $this;
    }

    /**
     * Returns the position index
     *
     * @return int
     */
    public function getPositionIndex(): int
    {
        return $this->positionIndex;
    }

    /**
     * Sets the position index
     *
     * @param int $index the position index
     *
     * @return $this
     */
    public function setPositionIndex(int $index): GroupElement
    {
        $this->positionIndex = $index;
        return $this;
    }

    /**
     * Returns the current encrypted entity
     *
     * @return null|EncryptedContent
     */
    public function getCurrentEncryptedEntity(): ?object
    {
        if (($object = $this->encryptedContents->last()) === false) {
            return null;
        }
        return $object;
    }

    /**
     * Return all intermediate keys
     *
     * @return ArrayCollection
     */
    public function getIntermediateKeys(): object
    {
        return $this->intermediateKeys;
    }
}
