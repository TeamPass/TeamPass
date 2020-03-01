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

namespace TeamPass\Core\Command;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\CommandController;
use TeamPass\ApiV1\Service\UserService;
use TeamPass\Core\Domain\Dto\Person;
use TeamPass\Core\Exception\UserException;

/**
 * Class UserCommandController
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 *
 * @Flow\Scope("singleton")
 */
class UserCommandController extends CommandController
{
    /**
     * @Flow\Inject
     * @var UserService
     */
    protected $userService;

    /**
     * creates a new user
     *
     * @param string $fullName the fullname
     * @param string $email the email address
     * @param string $username the username
     * @param string $password the password
     * @param string $group the group name
     *
     * @return void
     * @throws \Exception
     */
    public function createCommand(
        string $fullName,
        string $email,
        string $username,
        string $password,
        ?string $group = null
    ): void {
        try {
            $person = new Person();
            $person->setUsername($username);
            $person->setEmailAddress($email);
            $person->setFullName($fullName);
            $person->setNewPassword($password);

            $this->userService->create($person, $group);

            $this->outputLine("User '{$fullName}' with username '{$username}' successfully created!");
        } catch (UserException $e) {
            $this->outputLine("<b>Error: </b>" . $e->getMessage());
        }
    }
}
