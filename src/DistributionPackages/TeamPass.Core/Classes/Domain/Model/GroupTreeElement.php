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
 * Class GroupTreeElement
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 *
 * @Flow\Entity
 */
class GroupTreeElement
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
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    protected $name;

    /**
     * @ORM\OneToMany(mappedBy="parent")
     * @var Collection<GroupTreeElement>
     */
    protected $children;

    /**
     * @var GroupTreeElement
     * @ORM\ManyToOne(inversedBy="children")
     */
    protected $parent;

    /**
     * @var integer
     * @ORM\Column(type="integer",name="`index`")
     */
    protected $index;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $expanded = false;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $leaf;

    /**
     * @var bool
     * @ORM\Column(type="boolean", unique=true, nullable=true)
     */
    protected $isRoot;

    /**
     * @ORM\OneToMany(mappedBy="groupTreeElement")
     * @var Collection<Acl>
     **/
    protected $acls;

    /**
     * @ORM\OneToMany(mappedBy="groupTreeElement")
     * @var Collection<GroupElement>
     */
    protected $elements;

    /**
     * Constructs this book
     */
    public function __construct()
    {
        $this->acls = new ArrayCollection();
        $this->elements = new ArrayCollection();
        $this->children = new ArrayCollection();
    }

    /**
     * returns entity id
     *
     * @return int
     */
    public function getGroupTreeElementId(): int
    {
        return $this->id;
    }

    /**
     * Sets entity id
     *
     * @param int $id the group tree element id
     *
     * @return $this
     */
    public function setGroupTreeElementId(int $id): GroupTreeElement
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Returns name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets group tree element name
     *
     * @param string $name the group tree element name
     *
     * @return $this
     */
    public function setName(string $name): GroupTreeElement
    {
        $this->name = $name;
        return $this;
    }

    /**
     * returns index
     *
     * @return int $this->index
     */
    public function getIndex(): int
    {
        return $this->index;
    }

    /**
     * sets the position index
     *
     * @param int $index the position index
     *
     * @return $this
     */
    public function setIndex(int $index): GroupTreeElement
    {
        $this->index = $index;
        return $this;
    }

    /**
     * returns if TreeElement is expanded or not
     *
     * @return bool
     */
    public function getExpanded(): bool
    {
        return $this->expanded;
    }

    /**
     * set if TreeElement is expanded or not
     *
     * @param boolean $expanded value if group tree element is expanded or not
     *
     * @return $this
     */
    public function setExpanded(bool $expanded): GroupTreeElement
    {
        $this->expanded = $expanded;
        return $this;
    }

    /**
     * Returns the value of the class member leaf.
     *
     * @return bool
     */
    public function getLeaf(): bool
    {
        return $this->leaf;
    }

    /**
     * Sets the value for the class member $leaf.
     *
     * @param boolean $leaf flag if entity is leaf
     *
     * @return $this
     */
    public function setLeaf(bool $leaf): GroupTreeElement
    {
        $this->leaf = $leaf;
        return $this;
    }

    /**
     * returns all associated ACLs
     *
     * @return ArrayCollection
     */
    public function getAcls(): Collection
    {
        return $this->acls;
    }

    /**
     * Returns a collection containing all child elements
     *
     * @return ArrayCollection
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    /**
     * Returns parent entity
     *
     * @return object|null
     */
    public function getParent(): ?GroupTreeElement
    {
        return $this->parent;
    }

    /**
     * Sets parent group tree element
     *
     * @param GroupTreeElement $gte parent group tree element entity
     *
     * @return $this
     */
    public function setParent(GroupTreeElement $gte): GroupTreeElement
    {
        $this->parent = $gte;
        return $this;
    }

    /**
     * Returns a collection containing all associated elements
     *
     * @return ArrayCollection
     */
    public function getElements(): Collection
    {
        return $this->elements;
    }

    /**
     * Set root flag (only one entity)
     *
     * @return $this
     */
    public function setRoot(): GroupTreeElement
    {
        $this->isRoot = true;
        return $this;
    }

    /**
     * Returns if entity is root
     *
     * @return bool|null
     */
    public function getRoot(): ?bool
    {
        return $this->isRoot;
    }
}
