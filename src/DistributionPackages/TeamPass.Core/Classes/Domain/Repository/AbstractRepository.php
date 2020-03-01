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

namespace TeamPass\Core\Domain\Repository;

use Neos\Flow\Persistence\Exception\IllegalObjectTypeException;
use Neos\Flow\Persistence\Repository;
use TeamPass\Core\Exception\EntityNotFoundException;

/**
 * Class AbstractRepository
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */
abstract class AbstractRepository extends Repository
{
    /**
     * Loads a entity by id
     *
     * @param int $id entity id
     *
     * @throws \Exception
     * @return mixed
     */
    public function load(int $id): object
    {
        $result = $this->findByIdentifier($id);

        if (!$result) {
            throw new EntityNotFoundException("Entity with id '{$id}' not found");
        }

        return $result;
    }

    /**
     * Adds an object to this repository.
     *
     * @param object $object The object to add
     * @return void
     * @throws IllegalObjectTypeException
     * @api
     */
    public function add($object)
    {
        parent::add($object);
        $this->persistenceManager->persistAll();
    }

    /**
     * Schedules a modified object for persistence.
     *
     * @param object $object The modified object
     * @throws IllegalObjectTypeException
     * @api
     */
    public function update($object)
    {
        parent::update($object);
        $this->persistenceManager->persistAll();
    }

    /**
     * Removes an object from this repository.
     *
     * @param object $object The object to remove
     * @return void
     * @throws IllegalObjectTypeException
     * @api
     */
    public function remove($object)
    {
        parent::remove($object);
        $this->persistenceManager->persistAll();
    }
}
