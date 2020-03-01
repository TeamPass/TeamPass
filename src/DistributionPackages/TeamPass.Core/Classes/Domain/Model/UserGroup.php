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
 * Class UserGroup
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 *
 * @Flow\Entity
 */
class UserGroup
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
     * @ORM\Column(type="string")
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="boolean")
     * @var boolean
     */
    protected $admin = false;

    /**
     * @ORM\OneToMany(mappedBy="userGroup")
     * @var Collection<Acl>
     **/
    protected $acls;

    /**
     * @ORM\ManyToMany(mappedBy="groups")
     * @var Collection<User>
     */
    protected $users;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->acls = new ArrayCollection();
    }

    /**
     * returns id of this entity
     *
     * @return int
     */
    public function getUserGroupId(): int
    {
        return $this->id;
    }

    /**
     * returns the groupname
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * sets groupname
     *
     * @param string $name the group name
     *
     * @return $this
     */
    public function setName(string $name): object
    {
        $this->name = $name;
        return $this;
    }

    /**
     * returns ArrayCollection containing all users in this group
     *
     * @return ArrayCollection
     */
    public function getUsers(): object
    {
        return $this->users;
    }

    /**
     * Adds a user to this group
     *
     * @param User $user the user instance
     *
     * @return void
     */
    public function addUser(User $user): void
    {
        if (!$this->getUsers()->contains($user)) {
            $this->getUsers()->add($user);
        }
    }

    /**
     * Removes a user from this Group
     *
     * @param User $user the user instance
     *
     * @return $this
     */
    public function removeUser(User $user): object
    {
        if ($this->getUsers()->contains($user)) {
            $this->getUsers()->removeElement($user);
        }

        return $this;
    }

    /**
     * Return all acls bind to this group
     *
     * @return ArrayCollection
     */
    public function getAcls(): object
    {
        return $this->acls;
    }

    /**
     * flag if group has admin privileges
     *
     * @param boolean $flag true or false
     *
     * @return $this
     * @throws \Exception
     */
    public function setAdmin(bool $flag): object
    {
        $this->admin = $flag;
        return $this;
    }

    /**
     * Returns true if group is admin, false if not
     *
     * @return bool
     */
    public function getAdmin(): bool
    {
        return $this->admin;
    }

    /**
     * alias for getAdmin
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->getAdmin();
    }
}
