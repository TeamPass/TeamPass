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
 * Class Group
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */
class Group
{
    /**
     * @Flow\Validate(type="NotEmpty", validationGroups={"AdminUserGroupControllerUpdateAction"})
     *
     * @var int
     */
    protected $groupId;

    /**
     * @Flow\Validate(type="NotEmpty", validationGroups={"AdminUserGroupControllerUpdateAction"})
     * @Flow\Validate(type="NotEmpty", validationGroups={"AdminUserGroupControllerCreateAction"})
     *
     * @var string
     */
    protected $groupName;

    /**
     * @Flow\Validate(type="NotEmpty", validationGroups={"AdminUserGroupControllerUpdateAction"})
     * @Flow\Validate(type="NotEmpty", validationGroups={"AdminUserGroupControllerCreateAction"})
     *
     * @var bool
     */
    protected $isAdmin = false;

    /**
     * @Flow\Validate(type="NotEmpty", validationGroups={"AdminUserGroupControllerUpdateUserInGroupAction"})
     * @Flow\Validate(type="NotEmpty", validationGroups={"AdminUserGroupControllerDeleteUserFromGroupAction"})
     *
     * @var int
     */
    protected $userId;

    /**
     * @return string
     */
    public function getGroupName(): string
    {
        return $this->groupName;
    }

    /**
     * @param string $groupName
     */
    public function setGroupName(string $groupName): void
    {
        $this->groupName = $groupName;
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }

    /**
     * @param bool $isAdmin
     */
    public function setIsAdmin(bool $isAdmin): void
    {
        $this->isAdmin = $isAdmin;
    }

    /**
     * @return int
     */
    public function getGroupId(): ?int
    {
        return $this->groupId;
    }

    /**
     * @param string $groupId
     */
    public function setGroupId(string $groupId): void
    {
        if (is_numeric($groupId)) {
            $this->groupId = (int) $groupId;
        } else {
            $this->groupId = null;
        }
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }
}
