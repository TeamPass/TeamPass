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
use TeamPass\ApiV1\Service\UserGroupService;
use TeamPass\Core\Domain\Dto\Group;
use TeamPass\Core\Exception\UserException;

/**
 * Class GroupCommandController
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 *
 * @Flow\Scope("singleton")
 */

class GroupCommandController extends CommandController
{
    /**
     * @Flow\Inject
     * @var UserGroupService
     */
    protected $userGroupService;

    /**
     * creates a new group
     *
     * @param string $groupName the group name
     * @param string $isAdmin
     *
     * @return void
     * @throws \Exception
     */
    public function createCommand(string $groupName, string $isAdmin): void
    {
        try {
            if ($isAdmin === "yes" || $isAdmin === "ja" || $isAdmin === "1") {
                $isAdmin = true;
            } else {
                $isAdmin = false;
            }

            $groupDto = new Group();
            $groupDto->setGroupName($groupName);
            $groupDto->setIsAdmin($isAdmin);

            // create a new group based on given parameters
            $this->userGroupService->createGroup($groupDto);

            $this->outputLine("Group '{$groupName}' successfully created!");
        } catch (UserException $e) {
            $this->outputLine("<b>Error: </b>" . $e->getMessage());
        }
    }

    /**
     * adds a existing user to a existing group
     *
     * @param string $groupName the group name
     * @param string $userName  the user name
     *
     * @return void
     * @throws \Exception
     */
    public function addUserCommand($groupName, $userName): void
    {
        try {
            // create a new group based on given parameters
            $this->userGroupService->updateUserInGroupByNames($groupName, $userName);

            $this->outputLine("User <em>{$userName}</em> added to group <em>{$groupName}</em>");
        } catch (UserException $e) {
            $this->outputLine("<b>Error: </b>" . $e->getMessage());
        }
    }
}
