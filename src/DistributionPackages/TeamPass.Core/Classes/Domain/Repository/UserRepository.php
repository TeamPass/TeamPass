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

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Neos\Flow\Annotations as Flow;
use TeamPass\ApiV1\Service\AuthService;
use TeamPass\Core\Domain\Model\Directory;
use TeamPass\Core\Domain\Model\User;

/**
 * Class UserRepository
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 *
 * @Flow\Scope("singleton")
 */
class UserRepository extends AbstractRepository
{
    /**
     * returns user entity for given username if user is not deleted
     *
     * @param string $username
     *
     * @return object|null
     */
    public function getUserByUsername(string $username): ?object
    {
        $query = $this->createQuery();
        return $query->matching(
            $query->logicalAnd([
                $query->equals('username', $username),
                $query->logicalOr([
                    $query->equals('deleted', false),
                    $query->equals('deleted', null)
                ])
            ])
        )->execute()->getFirst();
    }

    /**
     * Returns the current amount of users
     *
     * @param bool $onlyComplete flag if only user with completed setup should be counted
     *
     * @return int
     */
    public function getTotalAmountOfUsers(bool $onlyComplete = false): int
    {
        $query = $this->createQuery();

        if ($onlyComplete) {
            $query->matching(
                $query->logicalNot(
                    $query->equals('privateKey', null)
                )
            );
        }

        return $query->execute()->count();
    }

    /**
     * Returns the current amount of users with admin privileges
     *
     * @param bool $onlyComplete flag if only user with completed setup should be counted
     *
     * @return int
     * @throws NonUniqueResultException
     */
    public function getTotalAmountOfAdmins(bool $onlyComplete = false): int
    {
        /** @var QueryBuilder $qb */
        $qb = $this->createQuery()->getQueryBuilder();
        $qb->select('count(u.id)')
            ->from('\TeamPass\Core\Domain\Model\UserGroup', 'ug')
            ->leftJoin('ug.users', 'u')
            ->andWhere('ug.admin = true');

        if ($onlyComplete) {
            $qb->andWhere($qb->expr()->isNotNull('u.privateKey'));
        }

        return (int)$qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Returns all available groups for given user
     *
     * @param int $userId the user id
     *
     * @return array
     */
    public function getAvailableGroupsForUser(int $userId): array
    {
        /** @var QueryBuilder $subQueryBuilder */
        $subQueryBuilder = $this->createQuery()->getQueryBuilder();
        $subQuery = $subQueryBuilder
            ->select(['ug.id'])
            ->from('\TeamPass\Core\Domain\Model\UserGroup', 'ug')
            ->innerJoin('ug.users', 'u')
            ->where('u.id = :userid')
            ->setParameter('userid', $userId)
            ->getQuery()
            ->getArrayResult();

        // the next query requires a multi dimensional array, but if user is not in any group only a single dimension
        // empty array will be returned
        // Probably a Doctrine bug
        if (empty($subQuery)) {
            $subQuery = array(array());
        }

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->createQuery()->getQueryBuilder();
        $query = $queryBuilder
            ->select(['c'])
            ->from('\TeamPass\Core\Domain\Model\UserGroup', 'c')
            ->where($queryBuilder->expr()->notIn('c.id', ':subQuery'))
            ->setParameter('subQuery', $subQuery)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * flag users as deleted if they don't exist in backend anymore
     *
     * @param array $users the previously fetched backend users
     * @param Directory $directory the current handled directory
     *
     * @return void
     */
    public function setDeletedFlagForDeletedUsers(array $users, Directory $directory): void
    {
        // get every Username from Backend
        foreach ($users as $row) {
            $userNames[] = $row[AuthService::USERNAME_ATTRIBUTE];
        }

        /** @var QueryBuilder $qb */
        $qb = $this->createQuery()->getQueryBuilder();
        $result = $qb->update('\TeamPass\Core\Domain\Model\User', 'u')
            ->set('u.deleted', '?1')
            ->andWhere('u.directory = :directory')
            ->andWhere('u.username NOT IN (:userNames)')
            ->setParameter('1', true)
            ->setParameter('directory', $directory)
            ->setParameter('userNames', $userNames)
            ->getQuery()
            ->getResult();
    }

    /**
     * unflag previous deleted flagged users if they exist in backend again
     *
     * @param array $users the previously fetched backend users
     * @param Directory $directory the current handled directory
     *
     * @return void
     */
    public function unsetDeletedFlagForDeletedUsers(array $users, Directory $directory): void
    {
        // get every Username from Backend
        foreach ($users as $row) {
            $userNames[] = $row[AuthService::USERNAME_ATTRIBUTE];
        }

        /** @var QueryBuilder $qb */
        $qb = $this->createQuery()->getQueryBuilder();
        $result = $qb->update('\TeamPass\Core\Domain\Model\User', 'u')
            ->set('u.deleted', '?1')
            ->andWhere('u.directory = :directory')
            ->andWhere('u.username IN (:userNames)')
            ->setParameter('1', false)
            ->setParameter('directory', $directory)
            ->setParameter('userNames', $userNames)
            ->getQuery()
            ->getResult();
    }
}
