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

use TeamPass\Core\Domain\Model\Directory;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\QueryInterface;

/**
 * Class DirectoryRepository
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 *
 * @Flow\Scope("singleton")
 */
class DirectoryRepository extends AbstractRepository
{
    /**
     * Returns the next free position index
     *
     * @return int
     * @throws NonUniqueResultException
     */
    public function getNextFreePositionIndex(): int
    {
        /** @var QueryBuilder $qb */
        $qb = $this->createQuery()->getQueryBuilder();

        $qb->select(['d'])
            ->from('\TeamPass\Core\Domain\Model\Directory', 'd')
            ->orderBy('d.positionIndex', 'DESC')
            ->setFirstResult(0)
            ->setMaxResults(1);

        $result = $qb->getQuery()->getOneOrNullResult();

        if ($result === null) {
            return 0;
        } else {
            $index = $result->getPositionIndex();
            return ++$index;
        }
    }

    /**
     * @return array
     */
    public function getExternalDirectories(): array
    {
        $query = $this->createQuery();
        return $query->matching(
            $query->equals('type', "external")
        )
            ->setOrderings(array('positionIndex' => QueryInterface::ORDER_ASCENDING))
            ->execute()
            ->toArray();
    }

    /**
     * @return Directory
     */
    public function getInternalDirectory(): object
    {
        $query = $this->createQuery();
        return $query->matching(
            $query->equals('type', "internal")
        )
            ->execute()
            ->getFirst();
    }

    /**
     * @return array
     */
    public function getAllDirectories(): array
    {
        $query = $this->createQuery();
        return $query->setOrderings(array('positionIndex' => QueryInterface::ORDER_ASCENDING))
            ->execute()
            ->toArray();
    }
}
