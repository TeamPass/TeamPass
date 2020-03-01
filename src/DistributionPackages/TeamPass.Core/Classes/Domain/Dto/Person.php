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
 * Class Person
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */
class Person
{
    /**
     * @Flow\Validate(type="NotEmpty", validationGroups={"AdminUserControllerUpdateAction"})
     *
     * @var int
     */
    protected $userId;

    /**
     * @Flow\Validate(type="NotEmpty", validationGroups={"AuthControllerLoginAction"})
     * @Flow\Validate(type="NotEmpty", validationGroups={"AdminUserControllerCreateAction"})
     *
     * @var string
     */
    protected $userName;

    /**
     * @var string
     */
    protected $fullName;

    /**
     * @var string
     */
    protected $emailAddress;

    /**
     * @Flow\Validate(type="NotEmpty", validationGroups={"AuthControllerLoginAction"})
     * @Flow\Validate(type="NotEmpty", validationGroups={"ManagementControllerChangePasswordAction"})
     *
     * @var string
     */
    protected $password;

    /**
     * @Flow\Validate(type="NotEmpty", validationGroups={"ManagementControllerChangePasswordAction"})
     *
     * @var string
     */
    protected $newPassword;

    /**
     * @Flow\Validate(type="NotEmpty", validationGroups={"AdminUserControllerCreateAction"})
     *
     * @var bool
     */
    protected $enabled;

    /**
     * @Flow\Validate(type="NotEmpty", validationGroups={"ManagementControllerChangePasswordAction"})
     *
     * @var string
     */
    protected $repeatedNewPassword;

    /**
     * @Flow\Validate(type="NotEmpty", validationGroups={"AuthControllerLoginAction"})
     *
     * @var string
     */
    protected $language;

    /**
     * @return int
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * @param string $userId
     */
    public function setUserId(string $userId): void
    {
        if (is_numeric($userId)) {
            $this->userId = (int) $userId;
        } else {
            $this->userId = null;
        }
    }

    /**
     * returns the username
     *
     * @return string
     */
    public function getUsername(): ?string
    {
        return $this->userName;
    }

    /**
     * sets the username
     *
     * @param string $userName the userName
     *
     * @return void
     */
    public function setUsername(string $userName): void
    {
        $this->userName = $userName;
    }

    /**
     * returns the password
     *
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * set the password
     *
     * @param string $password the password
     *
     * @return void
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    /**
     * @param string $newPassword new password
     *
     * @return void
     */
    public function setNewPassword(string $newPassword): void
    {
        $this->newPassword = $newPassword;
    }

    /**
     * @return string
     */
    public function getRepeatedNewPassword(): string
    {
        return $this->repeatedNewPassword;
    }

    /**
     * @param string $repeatedNewPassword
     *
     * @return void
     */
    public function setRepeatedNewPassword(string $repeatedNewPassword): void
    {
        $this->repeatedNewPassword = $repeatedNewPassword;
    }

    /**
     * @return string
     */
    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    /**
     * @param string $fullName
     */
    public function setFullName(string $fullName): void
    {
        $this->fullName = $fullName;
    }

    /**
     * @return string
     */
    public function getEmailAddress(): ?string
    {
        return $this->emailAddress;
    }

    /**
     * @param string $emailAddress
     */
    public function setEmailAddress(string $emailAddress): void
    {
        $this->emailAddress = $emailAddress;
    }

    /**
     * @return bool
     */
    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function setLanguage(string $language): void
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }
}
