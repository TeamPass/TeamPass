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

use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

/**
 * Class Acl
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 *
 * @Flow\Entity
 */
class Acl
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
     * @var UserGroup
     * @ORM\ManyToOne(inversedBy="acls")
     **/
    protected $userGroup;

    /**
     * @ORM\ManyToOne(inversedBy="acls")
     * @var GroupTreeElement
     **/
    protected $groupTreeElement;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    protected $pRead = false;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    protected $pCreate = false;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    protected $pUpdate = false;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    protected $pDelete = false;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    protected $inherited = false;

    /**
     * Return ID
     *
     * @return int
     */
    public function getAclId(): int
    {
        return $this->id;
    }


    /**
     * Set flag for read permission
     *
     * @param boolean $read the permission value
     *
     * @return $this
     */
    public function setRead(bool $read): Acl
    {
        $this->pRead = $read;
        return $this;
    }

    /**
     * get read permission
     *
     * @return boolean
     */
    public function getRead(): bool
    {
        return $this->pRead;
    }

    /**
     * set update permission flag
     *
     * @param boolean $update the permission value
     *
     * @return $this
     */
    public function setUpdate(bool $update): Acl
    {
        $this->pUpdate = $update;
        return $this;
    }

    /**
     * get update permission
     *
     * @return boolean
     */
    public function getUpdate(): bool
    {
        return $this->pUpdate;
    }

    /**
     * set create permission flag
     *
     * @param boolean $create the permission value
     *
     * @return $this
     */
    public function setCreate(bool $create): Acl
    {
        $this->pCreate = $create;
        return $this;
    }

    /**
     * get create permission
     *
     * @return boolean
     */
    public function getCreate(): bool
    {
        return $this->pCreate;
    }

    /**
     * set delete permission
     *
     * @param boolean $delete the permission value
     *
     * @return $this
     */
    public function setDelete(bool $delete): Acl
    {
        $this->pDelete = $delete;
        return $this;
    }

    /**
     * get delete permission
     *
     * @return boolean
     */
    public function getDelete(): bool
    {
        return $this->pDelete;
    }

    /**
     * Returns the bound groupTreeElement
     *
     * @return GroupTreeElement
     */
    public function getGroupTreeElement(): GroupTreeElement
    {
        return $this->groupTreeElement;
    }

    /**
     * Sets the group tree element
     *
     * @param GroupTreeElement $gte the group tree element
     *
     * @return $this
     */
    public function setGroupTreeElement(GroupTreeElement $gte): Acl
    {
        $this->groupTreeElement = $gte;
        return $this;
    }

    /**
     * get group
     *
     * @return mixed
     */
    public function getGroup(): UserGroup
    {
        return $this->userGroup;
    }

    /**
     * Sets the user group
     *
     * @param UserGroup $group the user group
     *
     * @return $this
     */
    public function setGroup(UserGroup $group): Acl
    {
        $this->userGroup = $group;
        return $this;
    }

    /**
     * Returns a permission value for given permission
     *
     * @param string $permission the permission
     *
     * @return bool
     * @throws \Exception
     */
    public function getPermission(string $permission): bool
    {
        switch ($permission) {
            case "create":
                return $this->getCreate();
                break;
            case "delete":
                return $this->getDelete();
                break;
            case "update":
                return $this->getUpdate();
                break;
            case "read":
                return $this->getRead();
                break;
            default:
                throw new \Exception("invalid permission requested");
        }
    }

    /**
     * Set flag if acl is inherited
     *
     * @param boolean $value a boolean flag
     *
     * @return $this
     * @throws \Exception
     */
    public function setInherited(bool $value): Acl
    {
        $this->inherited = $value;
        return $this;
    }

    /**
     * Returns inherited flag
     *
     * @return bool
     */
    public function getInherited(): bool
    {
        return $this->inherited;
    }
}
