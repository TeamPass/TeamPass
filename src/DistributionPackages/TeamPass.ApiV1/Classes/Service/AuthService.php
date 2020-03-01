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

namespace TeamPass\ApiV1\Service;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\QueryResultInterface;
use TeamPass\Core\Domain\Model\Directory;
use TeamPass\Core\Domain\Model\User;
use TeamPass\Core\Domain\Dto\Person;
use TeamPass\Core\Factory\AdapterFactory;
use TeamPass\Core\Interfaces\AdapterInterface;

/**
 * Class AuthService
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

class AuthService extends AbstractService
{
    /**
     * @var string
     */
    public const USERNAME_ATTRIBUTE = "userName";

    /**
     * @var string
     */
    public const EMAIL_ATTRIBUTE = "emailAddress";

    /**
     * @var string
     */
    public const FULLNAME_ATTRIBUTE = "fullName";

    /**
     * @Flow\Inject
     * @var AdapterFactory
     */
    protected $adapterFactory;

    /**
     * try to login on backend directory service
     *
     * @param Person $person the person value object
     *
     * @return object
     * @throws \Exception
     */
    public function login(Person $person): object
    {
        /** @var User $user */
        $user = $this->userRepository->getUserByUsername($person->getUsername());

        if ($user) {
            if (!$user->isEnabled()) {
                throw new \Exception("user {$person->getUsername()} not enabled");
            }

            if ($user->getDirectory()->getType() == "internal") {
                if ($user->checkPassword($person)) {
                    return $user;
                } else {
                    throw new \Exception("Password for user {$person->getUsername()} was invalid");
                }
            } else {
                /** @var AdapterInterface $adapter */
                $adapter = $this->setupExternalDirectoryConnection($user->getdirectory());
                $adapter->auth($person);
                return $user;
            }
        } else {
            throw new \Exception("User {$person->getUsername()} not found");
        }
    }

    /**
     * Start updating all Users coming from any external Source
     *
     * @return void
     * @throws \Exception
     */
    public function updateUsers(): void
    {
        // get all Directories in Database. already sorted by 'index' field
        $directories = $this->directoryRepository->findAll();

        // initiate an Update if Directory Type is external (e.g. if Directories is LDAP-Backend)
        /** @var Directory $directory */
        foreach ($directories as $directory) {
            if ($directory->getType() == "external") {
                $this->updateUsersFromExternalSource($directory);
            }
        }
    }

    /**
     * Updating Users coming from a external Source, based on their username
     * Updating and Creating a User could be consolidated
     *
     * @param Directory $directory the directory instance
     *
     * @return void
     * @throws \Exception
     */
    public function updateUsersFromExternalSource(Directory $directory): void
    {
        // get all users in Database
        $existingUsers = $this->userRepository->findAll();

        $adapter = $this->setupExternalDirectoryConnection($directory);
        $users = $adapter->fetchUsers();

        // CREATE: create all Users that are available in backend-directory but not in our local database
        $this->createUsersNotInDatabase($existingUsers, $users, $directory);

        $this->userRepository->setDeletedFlagForDeletedUsers($users, $directory);

        $this->userRepository->unsetDeletedFlagForDeletedUsers($users, $directory);

        /** @scrutinizer ignore-call */
        $intUserFromDirectory = $this->userRepository->findByDirectory($directory);
        // update User settings if anything has changed
        $this->updateUserSettingsFromDirectory($intUserFromDirectory, $users);
    }

    /**
     * initialize connection to directory backend by adapter class
     *
     * @param Directory $directory the directory
     *
     * @return AdapterInterface
     * @throws \Exception
     */
    protected function setupExternalDirectoryConnection(Directory $directory): object
    {
        $config = $directory->getConfiguration();

        // create adapter object
        $adapter = $this->adapterFactory->create($directory->getImplementationClass(), $config);
        $adapter->setup();

        return $adapter;
    }

    /**
     * creates all Users that are available in backend-directory but not in our local database
     *
     * @param QueryResultInterface     $doctrineObjects Collection of users
     * @param array     $directory       the user
     * @param Directory $directoryEntity the directory instance
     *
     * @return void
     * @throws \Exception
     */
    protected function createUsersNotInDatabase(
        QueryResultInterface $doctrineObjects,
        array $directory,
        Directory $directoryEntity
    ): void {
        $ar1 = array();
        $ar2 = array();
        $convertedUsers = array();

        // get every Username from Database and save it to a simple array
        foreach ($doctrineObjects as $userObj) {
            $ar1[] = $userObj->getUsername();
        }

        // get every Username from Backend plus create a converted Version of this Array, where username is a index
        // so we can find the user parameters easily
        foreach ($directory as $ar) {
            $ar2[] = $ar[AuthService::USERNAME_ATTRIBUTE];
            $convertedUsers[$ar[AuthService::USERNAME_ATTRIBUTE]] = $ar;
        }

        // save all users that not in Database in a new array
        $newUsers = array_diff($ar2, $ar1);

        // create new user
        foreach ($newUsers as $newUser) {
            $user = $convertedUsers[$newUser];

            /** @var User $userInstance */
            $instance = new User();
            $instance->setUsername($user[AuthService::USERNAME_ATTRIBUTE]);
            $instance->setEmail($user[AuthService::EMAIL_ATTRIBUTE]);
            $instance->setFullName($user[AuthService::FULLNAME_ATTRIBUTE]);
            $instance->setDirectory($directoryEntity);
            $instance->disable();

            $this->userRepository->add($instance);
        }
    }

    /**
     * Updates Email-Address and FullName if it has changed
     *
     * @param QueryResultInterface $doctrineObjects Collection of users
     * @param array $usersFromDirectory the directory instance
     *
     * @return void
     * @throws \Exception
     */
    protected function updateUserSettingsFromDirectory(
        QueryResultInterface $doctrineObjects,
        array $usersFromDirectory
    ): void {
        $ar1 = array();

        // get every Username from Database and save it to a simple array
        foreach ($doctrineObjects as $userObjKey => $userObj) {
            $ar1[$userObj->getUsername()] = $userObjKey;
        }

        foreach ($usersFromDirectory as $directoryUser) {
            $tmpUsername = $directoryUser[AuthService::USERNAME_ATTRIBUTE];
            $newSettings = array();

            if (isset($ar1[$tmpUsername])) {
                if ($directoryUser[AuthService::EMAIL_ATTRIBUTE] != $doctrineObjects[$ar1[$tmpUsername]]->getEmail()) {
                    $newSettings[AuthService::EMAIL_ATTRIBUTE] = $directoryUser[AuthService::EMAIL_ATTRIBUTE];
                }
                if (
                    $directoryUser[AuthService::FULLNAME_ATTRIBUTE] !=
                    $doctrineObjects[$ar1[$tmpUsername]]->getFullName()
                ) {
                    $newSettings[AuthService::FULLNAME_ATTRIBUTE] = $directoryUser[AuthService::FULLNAME_ATTRIBUTE];
                }

                /** @var User $user */
                $user = $doctrineObjects[$ar1[$tmpUsername]];

                if (isset($newSettings[AuthService::EMAIL_ATTRIBUTE])) {
                    $user->setEmail($newSettings[AuthService::EMAIL_ATTRIBUTE]);
                }

                if (isset($newSettings[AuthService::FULLNAME_ATTRIBUTE])) {
                    $user->setFullName($newSettings[AuthService::FULLNAME_ATTRIBUTE]);
                }

                $this->userRepository->update($user);
            }
        }
    }
}
