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
 * Class Permission
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */
class Permission
{
    /**
     * @var int
     * @Flow\Validate(type="NotEmpty", validationGroups={"AdminPermissionControllerUpdateAction"})
     * @Flow\Validate(type="NotEmpty", validationGroups={"AdminPermissionControllerDeleteAction"})
     */
    protected $id;

    /**
     * @var int
     * @Flow\Validate(type="NotEmpty", validationGroups={"AdminPermissionControllerCreateAction"})
     * @Flow\Validate(type="NotEmpty", validationGroups={"AdminPermissionControllerUpdateAction"})
     * @Flow\Validate(type="NotEmpty", validationGroups={"AdminPermissionControllerDeleteAction"})
     */
    protected $gteId;

    /**
     * @var int
     * @Flow\Validate(type="NotEmpty", validationGroups={"AdminPermissionControllerCreateAction"})
     * @Flow\Validate(type="NotEmpty", validationGroups={"AdminPermissionControllerUpdateAction"})
     * @Flow\Validate(type="NotEmpty", validationGroups={"AdminPermissionControllerDeleteAction"})
     */
    protected $userGroupId;

    /**
     * @var bool
     * @Flow\Validate(type="NotEmpty", validationGroups={"AdminPermissionControllerCreateAction"})
     * @Flow\Validate(type="NotEmpty", validationGroups={"AdminPermissionControllerUpdateAction"})
     * @Flow\Validate(type="NotEmpty", validationGroups={"AdminPermissionControllerDeleteAction"})
     */
    protected $pCreate = false;

    /**
     * @var bool
     * @Flow\Validate(type="NotEmpty", validationGroups={"AdminPermissionControllerCreateAction"})
     * @Flow\Validate(type="NotEmpty", validationGroups={"AdminPermissionControllerUpdateAction"})
     * @Flow\Validate(type="NotEmpty", validationGroups={"AdminPermissionControllerDeleteAction"})
     */
    protected $pDelete = false;

    /**
     * @var bool
     * @Flow\Validate(type="NotEmpty", validationGroups={"AdminPermissionControllerCreateAction"})
     * @Flow\Validate(type="NotEmpty", validationGroups={"AdminPermissionControllerUpdateAction"})
     * @Flow\Validate(type="NotEmpty", validationGroups={"AdminPermissionControllerDeleteAction"})
     */
    protected $pRead = false;

    /**
     * @var bool
     * @Flow\Validate(type="NotEmpty", validationGroups={"AdminPermissionControllerCreateAction"})
     * @Flow\Validate(type="NotEmpty", validationGroups={"AdminPermissionControllerUpdateAction"})
     * @Flow\Validate(type="NotEmpty", validationGroups={"AdminPermissionControllerDeleteAction"})
     */
    protected $pUpdate = false;

    /**
     * @return int
     */
    public function getGteId(): int
    {
        return $this->gteId;
    }

    /**
     * @param int $gteId
     */
    public function setGteId(int $gteId): void
    {
        $this->gteId = $gteId;
    }

    /**
     * @return int
     */
    public function getUserGroupId(): int
    {
        return $this->userGroupId;
    }

    /**
     * @param int $userGroupId
     */
    public function setUserGroupId(int $userGroupId): void
    {
        $this->userGroupId = $userGroupId;
    }

    /**
     * @return bool
     */
    public function isPCreate(): bool
    {
        return $this->pCreate;
    }

    /**
     * @param bool $pCreate
     */
    public function setPCreate(bool $pCreate): void
    {
        $this->pCreate = $pCreate;
    }

    /**
     * @return bool
     */
    public function isPDelete(): bool
    {
        return $this->pDelete;
    }

    /**
     * @param bool $pDelete
     */
    public function setPDelete(bool $pDelete): void
    {
        $this->pDelete = $pDelete;
    }

    /**
     * @return bool
     */
    public function isPRead(): bool
    {
        return $this->pRead;
    }

    /**
     * @param bool $pRead
     */
    public function setPRead(bool $pRead): void
    {
        $this->pRead = $pRead;
    }

    /**
     * @return bool
     */
    public function isPUpdate(): bool
    {
        return $this->pUpdate;
    }

    /**
     * @param bool $pUpdate
     */
    public function setPUpdate(bool $pUpdate): void
    {
        $this->pUpdate = $pUpdate;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
