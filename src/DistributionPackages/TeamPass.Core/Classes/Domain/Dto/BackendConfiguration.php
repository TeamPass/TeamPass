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
 * Class BackendConfiguration
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */
class BackendConfiguration
{
    /**
     * @var string
     */
    protected $hostname;

    /**
     * @var string
     */
    protected $security;

    /**
     * @var int
     */
    protected $port;

    /**
     * @var boolean
     */
    protected $anonymous;

    /**
     * @var string
     */
    protected $ldapAdminDn;

    /**
     * @var string
     */
    protected $ldapAdminPassword;

    /**
     * @var string
     */
    protected $ldapBasedn;

    /**
     * @var int
     */
    protected $ldapSynchroniseIntervalInMin;

    /**
     * @var int
     */
    protected $ldapReadTimeoutInSec;

    /**
     * @var string
     */
    protected $ldapUserObjectclass;

    /**
     * @var string
     */
    protected $ldapUserFilter;

    /**
     * @var string
     */
    protected $ldapUserUsername;

    /**
     * @var string
     */
    protected $ldapUserDisplayname;

    /**
     * @var string
     */
    protected $ldapUserEmail;

    /**
     * @var string
     */
    protected $ldapUserDn;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var int
     */
    protected $ldapCacheSynchroniseIntervalInMin;

    /**
     * @return string
     */
    public function getHostname(): string
    {
        return $this->hostname;
    }

    /**
     * @param string $hostname
     */
    public function setHostname(string $hostname): void
    {
        $this->hostname = $hostname;
    }

    /**
     * @return string
     */
    public function getSecurity(): string
    {
        return $this->security;
    }

    /**
     * @param string $security
     */
    public function setSecurity(string $security): void
    {
        $this->security = $security;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @param int $port
     */
    public function setPort(int $port): void
    {
        $this->port = $port;
    }

    /**
     * @return bool
     */
    public function isAnonymous(): bool
    {
        return $this->anonymous;
    }

    /**
     * @param bool $anonymous
     */
    public function setAnonymous(bool $anonymous): void
    {
        $this->anonymous = $anonymous;
    }

    /**
     * @return string
     */
    public function getLdapAdminDn(): string
    {
        return $this->ldapAdminDn;
    }

    /**
     * @param string $ldapAdminDn
     */
    public function setLdapAdminDn(string $ldapAdminDn): void
    {
        $this->ldapAdminDn = $ldapAdminDn;
    }

    /**
     * @return string
     */
    public function getLdapAdminPassword(): string
    {
        return $this->ldapAdminPassword;
    }

    /**
     * @param string $ldapAdminPassword
     */
    public function setLdapAdminPassword(string $ldapAdminPassword): void
    {
        $this->ldapAdminPassword = $ldapAdminPassword;
    }

    /**
     * @return string
     */
    public function getLdapBasedn(): string
    {
        return $this->ldapBasedn;
    }

    /**
     * @param string $ldapBasedn
     */
    public function setLdapBasedn(string $ldapBasedn): void
    {
        $this->ldapBasedn = $ldapBasedn;
    }

    /**
     * @return int
     */
    public function getLdapSynchroniseIntervalInMin(): int
    {
        return $this->ldapSynchroniseIntervalInMin;
    }

    /**
     * @param int $ldapSynchroniseIntervalInMin
     */
    public function setLdapSynchroniseIntervalInMin(int $ldapSynchroniseIntervalInMin): void
    {
        $this->ldapSynchroniseIntervalInMin = $ldapSynchroniseIntervalInMin;
    }

    /**
     * @return int
     */
    public function getLdapReadTimeoutInSec(): int
    {
        return $this->ldapReadTimeoutInSec;
    }

    /**
     * @param int $ldapReadTimeoutInSec
     */
    public function setLdapReadTimeoutInSec(int $ldapReadTimeoutInSec): void
    {
        $this->ldapReadTimeoutInSec = $ldapReadTimeoutInSec;
    }

    /**
     * @return string
     */
    public function getLdapUserObjectclass(): string
    {
        return $this->ldapUserObjectclass;
    }

    /**
     * @param string $ldapUserObjectclass
     */
    public function setLdapUserObjectclass(string $ldapUserObjectclass): void
    {
        $this->ldapUserObjectclass = $ldapUserObjectclass;
    }

    /**
     * @return string
     */
    public function getLdapUserFilter(): string
    {
        return $this->ldapUserFilter;
    }

    /**
     * @param string $ldapUserFilter
     */
    public function setLdapUserFilter(string $ldapUserFilter): void
    {
        $this->ldapUserFilter = $ldapUserFilter;
    }

    /**
     * @return string
     */
    public function getLdapUserUsername(): string
    {
        return $this->ldapUserUsername;
    }

    /**
     * @param string $ldapUserUsername
     */
    public function setLdapUserUsername(string $ldapUserUsername): void
    {
        $this->ldapUserUsername = $ldapUserUsername;
    }

    /**
     * @return string
     */
    public function getLdapUserDisplayname(): string
    {
        return $this->ldapUserDisplayname;
    }

    /**
     * @param string $ldapUserDisplayname
     */
    public function setLdapUserDisplayname(string $ldapUserDisplayname): void
    {
        $this->ldapUserDisplayname = $ldapUserDisplayname;
    }

    /**
     * @return string
     */
    public function getLdapUserEmail(): string
    {
        return $this->ldapUserEmail;
    }

    /**
     * @param string $ldapUserEmail
     */
    public function setLdapUserEmail(string $ldapUserEmail): void
    {
        $this->ldapUserEmail = $ldapUserEmail;
    }

    /**
     * @return string
     */
    public function getLdapUserDn(): string
    {
        return $this->ldapUserDn;
    }

    /**
     * @param string $ldapUserDn
     */
    public function setLdapUserDn(string $ldapUserDn): void
    {
        $this->ldapUserDn = $ldapUserDn;
    }

    /**
     * @return int
     */
    public function getLdapCacheSynchroniseIntervalInMin(): int
    {
        return $this->ldapCacheSynchroniseIntervalInMin;
    }

    /**
     * @param int $ldapCacheSynchroniseIntervalInMin
     */
    public function setLdapCacheSynchroniseIntervalInMin(int $ldapCacheSynchroniseIntervalInMin): void
    {
        $this->ldapCacheSynchroniseIntervalInMin = $ldapCacheSynchroniseIntervalInMin;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return get_object_vars($this);
    }

    /**
     * @param array $params
     *
     * @return void
     */
    public function setParameters(array $params): void
    {
        foreach ($params as $key => $value) {
            $this->$key = $value;
        }
    }
}
