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
use TeamPass\Core\Domain\Dto\Backend;
use TeamPass\Core\Domain\Dto\BackendConfiguration;
use TeamPass\Core\Domain\Model\Directory;
use TeamPass\Core\Domain\Repository\DirectoryRepository;
use TeamPass\Core\Exception\ParameterValidationException;
use TeamPass\Core\Factory\AdapterFactory;
use TeamPass\Core\Factory\ImplementationClassFactory;
use TeamPass\Core\Interfaces\AdapterImplementationClassInterface;

/**
 * Class DirectoryService
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

class DirectoryService extends AbstractService
{
    /**
     * @var int
     */
    protected const USER_LIMIT = 100;

    /**
     * @Flow\Inject
     * @var AuthService
     */
    protected $authService;

    /**
     * @Flow\Inject
     * @var AdapterFactory
     */
    protected $adapterFactory;

    /**
     * @Flow\Inject
     * @var ImplementationClassFactory
     */
    protected $implementationClassFactory;

    /**
     * Returns all directories as a grid array
     *
     * @return array
     * @throws \Exception
     */
    public function getAllDirectoriesAsGrid(): array
    {
        $this->sortDirectories();

        $directories = $this->directoryRepository->getAllDirectories();
        $result = array();

        /* @var Directory $directory */
        foreach ($directories as $directory) {
            $ar = array();
            $ar['directoryId'] = $directory->getDirectoryId();
            $ar['directoryName'] = $directory->getDirectoryName();
            $ar['type'] = $directory->getType();
            $ar['positionIndex'] = $directory->getPositionIndex();
            $ar['configuration'] = $directory->getConfiguration();
            $ar['adapter'] = $directory->getImplementationClass();
            $result[] = $ar;
        }
        return $result;
    }

    /**
     * Returns a external directory based on given directory id
     *
     * @param int $directoryId a directory id
     *
     * @return array
     * @throws \Exception
     */
    public function getDirectory(int $directoryId): array
    {
        $result = array();

        /** @var Directory $directory */
        $directory = $this->directoryRepository->load($directoryId);

        if ($directory->getType() === "internal") {
            throw new \Exception("internal directory is not editable");
        }

        $result['directoryId'] = $directory->getDirectoryId();
        $result['name'] = $directory->getDirectoryName();
        $result['implementationClass'] = $directory->getImplementationClass();
        $result['type'] = $directory->getType();

        return array_merge($result, $directory->getConfiguration());
    }

    /**
     * Returns all directories as a grid array
     *
     * @return array
     */
    public function getExternalDirectoriesAsGrid(): array
    {
        $directories = $this->directoryRepository->getExternalDirectories();
        $result = array();

        /* @var Directory $directory */
        foreach ($directories as $directory) {
            $ar = array();
            $ar['id'] = $directory->getDirectoryId();
            $ar['name'] = $directory->getDirectoryName();
            $ar['implementationClass'] = $directory->getImplementationClass();
            $result[] = $ar;
        }
        return $result;
    }

    /**
     * Returns all directories as tree array
     *
     * @return array
     * @throws \Exception
     */
    public function getAllDirectoriesAsTree(): array
    {
        $this->sortDirectories();

        $directories = $this->directoryRepository->getAllDirectories();
        $result = array();

        /* @var Directory $directory */
        foreach ($directories as $directory) {
            $ar = array();
            $ar['directoryId'] = $directory->getDirectoryId();
            $ar['directoryName'] = $directory->getDirectoryName();
            $ar['type'] = $directory->getType();
            $ar['positionIndex'] = $directory->getPositionIndex();
            $ar['configuration'] = $directory->getConfiguration();
            $ar['adapter'] = $directory->getImplementationClass();
            $ar['leaf'] = true;
            $result[] = $ar;
        }

        $root = array();
        $root['title'] = 'root';
        $root['children'] = $result;

        return $root;
    }

    /**
     * creates a new directory based on given parameters
     *
     * @param Backend $backend the backend dto
     * @param BackendConfiguration $backendConfiguration the backend configuration dto
     *
     * @return int
     * @throws \Exception
     */
    public function createDirectory(Backend $backend, BackendConfiguration $backendConfiguration): int
    {
        // checks if backend configuration is complete
        $this->validateParameters($backend->getImplementationClass(), $backendConfiguration);

        /** @scrutinizer ignore-call */
        $directory = $this->directoryRepository->findOneByName($backend->getName());

        if ($directory instanceof Directory) {
            throw new \Exception("creating Directory failed: Directory '{$backend->getName()}' already exists");
        }

        if ($backend->getType() === Directory::DEFAULT_INTERNAL_DIRECTORY_NAME) {
            $directory = $this->directoryRepository->getInternalDirectory();

            if ($directory instanceof Directory) {
                throw new \Exception("creating directory failed: 'internal' directory already exists");
            }
        }

        /** @var DirectoryRepository $directoryRepository */
        $positionIndex = $this->directoryRepository->getNextFreePositionIndex();

        $directory = new Directory();
        $directory->setDirectoryName($backend->getName())
                ->setType($backend->getType())
                ->setPositionIndex($positionIndex)
                ->setImplementationClass($backend->getImplementationClass())
                ->setConfiguration($backendConfiguration->getParameters());

        $this->directoryRepository->add($directory);
        // persist entity to get entity id
        $this->persistenceManager->persistAll();

        return $directory->getDirectoryId();
    }

    /**
     * updates a directory with given parameters
     *
     * @param Backend $backend the backend dto
     * @param BackendConfiguration $backendConfiguration the backend configuration dto
     *
     * @return bool
     * @throws \Exception
     */
    public function updateDirectory(Backend $backend, BackendConfiguration $backendConfiguration): bool
    {
        /** @var Directory $directory */
        $directory = $this->directoryRepository->load($backend->getDirectoryId());

        /** @scrutinizer ignore-call */
        $directoryCheck = $this->directoryRepository->findOneByName($backend->getName());

        if (
            $directoryCheck instanceof Directory &&
            $directoryCheck->getDirectoryId() !== $directory->getDirectoryId() &&
            $directoryCheck->getDirectoryName() !== $directory->getDirectoryName()
        ) {
            throw new \Exception("update Directory failed: Directory name '{$backend->getName()}' already exists");
        }

        $directory->setDirectoryName($backend->getName());
        $directory->setConfiguration($backendConfiguration->getParameters());

        $this->directoryRepository->update($directory);

        return true;
    }

    /**
     * deletes a directory based on given id
     *
     * @param integer $directoryId a directory id
     *
     * @return bool
     * @throws \Exception
     */
    public function deleteDirectory(int $directoryId): bool
    {
        /* @var Directory $directory */
        $directory = $this->directoryRepository->load($directoryId);

        if ($directory->getUsers()->count() > 0) {
            throw new \Exception("failed to delete directory: some users still connected");
        }

        $this->directoryRepository->remove($directory);

        // update the position index for all directories
        $this->sortDirectories();

        return true;
    }

    /**
     * validates the given parameters
     *
     * @param string $implementationClass the implementation class
     * @param BackendConfiguration $backendConfiguration the configuration dto to validate
     *
     * @return void
     * @throws ParameterValidationException
     * @throws \Exception
     */
    protected function validateParameters(string $implementationClass, BackendConfiguration $backendConfiguration): void
    {
        $result = array();
        $presets = $this->getDirectoryPresets($implementationClass);
        $configurationParameters = $backendConfiguration->getParameters();

        foreach ($presets as $presetKey => $presetValue) {
            if (!isset($configurationParameters[$presetKey])) {
                throw new ParameterValidationException("Parameter '{ $presetKey }' is not set!");
            }
            if (is_string($configurationParameters[$presetKey])) {
                $result[$presetKey] = addslashes($configurationParameters[$presetKey]);
            } else {
                $result[$presetKey] = $configurationParameters[$presetKey];
            }
        }

        $backendConfiguration->setParameters($result);
    }

    /**
     * change index position of given directory
     *
     * @param integer $directoryId a directory id
     *
     * @return void
     * @throws \Exception
     */
    public function changeIndexUp(int $directoryId): void
    {
        $this->changeIndex($directoryId, "up");
    }

    /**
     * change index position of given directory
     *
     * @param integer $directoryId a directory id
     *
     * @return void
     * @throws \Exception
     */
    public function changeIndexDown(int $directoryId): void
    {
        $this->changeIndex($directoryId, "down");
    }


    /**
     * change index position of given directory
     *
     * @param integer $directoryId a directory id
     * @param string  $direction   the direction, "up" or "down"
     *
     * @return void
     * @throws \Exception
     */
    protected function changeIndex(int $directoryId, string $direction): void
    {
        $directories = $this->directoryRepository->findAll();
        $ar = array();

        /** @var Directory $directory */
        foreach ($directories as $directory) {
            $ar[$directory->getPositionIndex()] = $directory->getDirectoryId();
        }
        // ensure the given directory id exists
        if (array_search($directoryId, $ar) === false) {
            throw new \Exception("failed to move Directory: id '{$directoryId}' does not exist");
        }

        //sort array based on position index
        ksort($ar);
        // copy array
        $compareableArray = $ar;
        // invert the array
        $ar = array_flip($ar);
        // reindex the array
        $compareableArray = array_values($compareableArray);
        // search position index for given directory id
        $index = array_search($directoryId, $compareableArray);

        if ($direction == "down") {
            // check if direcotry is already on last postition
            if (!isset($compareableArray[$index + 1])) {
                throw new \Exception("Directory with id '{$directoryId}' is already on last position");
            }
            $tmp = $compareableArray[$index + 1];
            $compareableArray[$index + 1] = $compareableArray[$index];
            $compareableArray[$index] = $tmp;
        }

        if ($direction == "up") {
            // check if direcotry is already on last postition
            if (!isset($compareableArray[$index - 1])) {
                throw new \Exception("Directory with id '{$directoryId}' is already on top position");
            }
            $tmp = $compareableArray[$index - 1];
            $compareableArray[$index - 1] = $compareableArray[$index];
            $compareableArray[$index] = $tmp;
        }

        // invert the array
        $compareableArray = array_flip($compareableArray);

        // contains all directories which should get a new position index
        $dirtyIndicies = array_diff_assoc($compareableArray, $ar);

        /** @var Directory $directory */
        foreach ($directories as $directory) {
            if (isset($dirtyIndicies[$directory->getDirectoryId()])) {
                $directory->setPositionIndex($dirtyIndicies[$directory->getDirectoryId()]);

                $this->directoryRepository->update($directory);
            }
        }
    }

    /**
     * sort all directory and change position index if necessary
     *
     * @return void
     */
    protected function sortDirectories(): void
    {
        $directories = $this->directoryRepository->findAll();

        /** @var Directory $directory */
        foreach ($directories as $directory) {
            $ar[$directory->getPositionIndex()] = $directory->getDirectoryId();
        }

        //sort array based on position index
        ksort($ar);
        // clone array
        $compareableArray = $ar;
        // invert the array
        $ar = array_flip($ar);
        // reindex the array
        $compareableArray = array_values($compareableArray);
        // invert the array
        $compareableArray = array_flip($compareableArray);
        // contains all directories which should get a new position index
        //$dirtyIndicies = array_diff($compareableArray, $ar);
        $dirtyIndicies = array_diff_assoc($compareableArray, $ar);

        foreach ($directories as $directory) {
            if (isset($dirtyIndicies[$directory->getDirectoryId()])) {
                $directory->setPositionIndex($dirtyIndicies[$directory->getDirectoryId()]);
                #$this->entityManager->merge($directory);
            }
        }
        #$this->entityManager->flush();
    }

    /**
     * starts a directory sync for given directory id
     *
     * @param integer $directoryId the directory id
     *
     * @return void
     * @throws \Exception
     */
    public function sync(int $directoryId): void
    {
        /* @var Directory $directory */
        $directory = $this->directoryRepository->load($directoryId);
        $this->authService->updateUsersFromExternalSource($directory);
    }

    /**
     * Returns the internal directory
     *
     * @return Directory
     * @throws \Exception
     */
    public function getLocalDirectory(): Directory
    {
        return $this->directoryRepository->getInternalDirectory();
    }

    /**
     * Returns the preset values for given implementation class
     *
     * @param string $implementationClass the implementation class
     *
     * @return array
     * @throws \Exception
     */
    public function getDirectoryPresets(string $implementationClass): array
    {
        /** @var AdapterImplementationClassInterface $ic */
        $ic = $this->implementationClassFactory->create($implementationClass);
        return $ic->getPresetValues();
    }

    /**
     * evaluates the given connection details
     *
     * @param Backend $backend the backend dto
     * @param BackendConfiguration $backendConfiguration the backend config dto
     *
     * @return array
     * @throws \Exception
     */
    public function evalConnection(Backend $backend, BackendConfiguration $backendConfiguration): array
    {
        // create adapter object
        $adapter = $this->adapterFactory->create(
            $backend->getImplementationClass(),
            $backendConfiguration->getParameters()
        );
        $adapter->setup();

        // multidimensional array with username attribute as array key
        $users = $adapter->fetchUsers(self::USER_LIMIT);

        $result = array();

        // strip the array key
        foreach ($users as $user) {
            $result[] = $user;
        }

        return $result;
    }
}
