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

namespace TeamPass\Core\Adapter\ImplementationClass;

use TeamPass\Core\Interfaces\AdapterImplementationClassInterface;

/**
 * Class OpenLdapImplementation
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

class OpenLdapImplementation implements AdapterImplementationClassInterface
{
    /**
     * Returns the adapter class name implementation
     *
     * @return string
     */
    public function getAdapter(): string
    {
        return "LdapAdapter";
    }

    /**
     * returns the preset values for this implementation
     *
     * @return array
     */
    public function getPresetValues(): array
    {
        return array(
            "hostname" => "",
            "port" => 389,
            "security" => "none",
            "anonymous" => false,
            "ldapAdminDn" => "",
            "ldapAdminPassword" => "",
            "ldapBasedn" => "",
            "ldapUserDn" => "",
            "ldapCacheSynchroniseIntervalInMin" => 60,
            "ldapReadTimeoutInSec" => 120,
            "ldapUserObjectclass" => "user",
            "ldapUserFilter" => "(&(objectClass=posixAccount)(uid=*))",
            "ldapUserUsername" => "uid",
            "ldapUserDisplayname" => "cn",
            "ldapUserEmail" => "mail"
        );
    }
}
