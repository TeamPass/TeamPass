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

use Doctrine\ORM\QueryBuilder;
use Neos\Flow\Annotations as Flow;
use TeamPass\Core\Domain\Model\GroupTreeElement;
use TeamPass\Core\Exception\InsufficientPermissionException;
use TeamPass\Core\Domain\Model\Acl;

/**
 * Class AclRepository
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 *
 * @Flow\Scope("singleton")
 */
class AclRepository extends AbstractRepository
{
    /**
     * Check if user has requested action permission for group tree element
     *
     * @param int    $userId             the userid
     * @param string $action             the action
     * @param int    $groupTreeElementId the grouptreeelement id
     *
     * @return bool
     * @throws InsufficientPermissionException
     * @throws \Exception
     */
    public function checkPermission(int $userId, string $action, int $groupTreeElementId): bool
    {
        // check if user is admin
        if ($this->isAdmin($userId, false)) {
            return true;
        }

        /** @var QueryBuilder $qb */
        $qb = $this->createQuery()->getQueryBuilder();
        $qb->select('a')
            ->from('\TeamPass\Core\Domain\Model\Acl', 'a')
            ->leftJoin('a.userGroup', 'g')
            ->andWhere(':user MEMBER OF g.users')
            ->andWhere('a.groupTreeElement = :groupTreeElementId')
            ->andWhere('a.groupTreeElement = :groupTreeElementId')
            ->setParameter('user', $userId)
            ->setParameter('groupTreeElementId', $groupTreeElementId);


        $acls = $qb->getQuery()->getResult();
        /** @var Acl $acl */
        foreach ($acls as $acl) {
            if ($acl->getPermission($action)) {
                return true;
            }
        }
        throw new InsufficientPermissionException("Permission denied");
    }

    /**
     * Checks if user s admin
     *
     * @param integer $userId the user-id
     * @param bool    $strict flag if a false or a exception should returned in failure
     *
     * @return bool
     * @throws InsufficientPermissionException
     */
    public function isAdmin(int $userId, bool $strict = true): bool
    {
        /** @var QueryBuilder $qb */
        $qb = $this->createQuery()->getQueryBuilder();
        $qb->select('u')
            ->from('\TeamPass\Core\Domain\Model\User', 'u')
            ->leftJoin('u.groups', 'g')
            ->andWhere('u.id = :user')
            ->andWhere('g.admin = 1')
            ->setParameter('user', $userId);

        $res = $qb->getQuery()->getResult();

        $count = count($res);

        if ($count === 1) {
            return true;
        } elseif ($count === 0) {
            if ($strict === true) {
                throw new InsufficientPermissionException("Permission denied");
            }
        }

        return false;
    }

    /**
     * Returns all ACLs for given group tree element
     *
     * @param GroupTreeElement $groupTreeElement the group tree element
     *
     * @return array
     */
    public function fetchPermissions(GroupTreeElement $groupTreeElement): array
    {
        /** @var QueryBuilder $qb */
        $qb = $this->createQuery()->getQueryBuilder();
        $qb->select('a')
            ->from('\TeamPass\Core\Domain\Model\Acl', 'a')
            ->where('a.groupTreeElement = :groupTreeElement')
            ->setParameter('groupTreeElement', $groupTreeElement);

        return $qb->getQuery()->getResult();
    }

    /**
     * Returns all ACLs for given user
     *
     * @param int $userId the user id
     *
     * @return array
     */
    public function fetchPermissionsForUser(int $userId): array
    {
        /** @var QueryBuilder $qb */
        $qb = $this->createQuery()->getQueryBuilder();
        $qb->select('a')
            ->from('\TeamPass\Core\Domain\Model\Acl', 'a')
            ->join('a.group', 'g')
            ->join('g.users', 'u')
            ->andWhere('u.id = :user')
            ->setParameter('user', $userId);

        return $qb->getQuery()->getResult();
    }

    /**
     * Returns all ACLs for given user
     *
     * @param int $gteId  the group tree element id
     * @param int $userId the user id
     *
     * @return array
     */
    public function fetchUserPermissionsForGte(int $gteId, int $userId): array
    {
        /** @var QueryBuilder $qb */
        $qb = $this->createQuery()->getQueryBuilder();
        $qb->select('a')
            ->from('\TeamPass\Core\Domain\Model\Acl', 'a')
            ->join('a.userGroup', 'ug')
            ->join('ug.users', 'u')
            ->andWhere('u.id = :user')
            ->andWhere('a.groupTreeElement = :groupTreeElement')
            ->setParameter('user', $userId)
            ->setParameter('groupTreeElement', $gteId);

        return $qb->getQuery()->getResult();
    }

    /**
     *
     * @param int $userGroupId the user group id
     * @param int $groupTreeElementId the group tree element id
     *
     * @return void
     * @throws \Exception
     */
    public function failIfExists(int $userGroupId, int $groupTreeElementId): void
    {
        $query = $this->createQuery();

        $result =  $query->matching(
            $query->logicalAnd([
                $query->equals('userGroup', $userGroupId),
                $query->equals('groupTreeElement', $groupTreeElementId)
            ])
        )
            ->execute();

        if ($result->count() !== 0) {
            throw new \Exception(
                "Acl for userGroup '{$userGroupId}' and groupTreeElement '{$groupTreeElementId}' already exists"
            );
        }
    }
}
