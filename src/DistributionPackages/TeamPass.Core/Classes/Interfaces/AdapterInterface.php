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

namespace TeamPass\Core\Interfaces;

use TeamPass\Core\Adapter\Model\Configuration;
use TeamPass\Core\Domain\Dto\Person;

/**
 * Interface AdapterInterface
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

interface AdapterInterface
{

    /**
     * Sets the implementation class object
     *
     * @param AdapterImplementationClassInterface $implementationClass the implementation class object
     *
     * @return void
     */
    public function setImplementationClass(AdapterImplementationClassInterface $implementationClass);

    /**
     * Sets adapter configuration
     *
     * @param Configuration $configuration the configuration model
     *
     * @return void
     */
    public function setConfiguration(Configuration $configuration);

    /**
     * Establish initial Connection
     *
     * @return void
     * @throws \Exception
     */
    public function setup();

    /**
     * Authenticate against adapter backend
     *
     * @param Person $person the person value obejct
     *
     * @return boolean true if the login was successful, else false
     */
    public function auth(Person $person);

    /**
     * Fetch all Users (username, email, display name)
     *
     * @param int $userLimit the max amount of user
     *
     * @return array
     */
    public function fetchUsers(int $userLimit = 0);
}
