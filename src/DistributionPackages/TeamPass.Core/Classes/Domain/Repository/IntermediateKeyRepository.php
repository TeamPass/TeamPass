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

use Neos\Flow\Annotations as Flow;
use TeamPass\Core\Domain\Model\IntermediateKey;

/**
 * Class IntermediateKeyRepository
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 *
 * @Flow\Scope("singleton")
 */
class IntermediateKeyRepository extends AbstractRepository
{
    /**
     * Returns the intermediate key entity instance for user with group element
     *
     * @param int $userId         the user id
     * @param int $groupElementId the group element id
     *
     * @return mixed
     * @throws \Exception
     */
    public function getIntermediateKeyForUser(int $userId, int $groupElementId)
    {
        $query = $this->createQuery();
        $res = $query->matching(
            $query->logicalAnd([
                $query->equals('groupElement', $groupElementId),
                $query->equals('user', $userId)
                ])
        )
            ->execute()->toArray();

        $count = count($res);

        if ($count === 0) {
            throw new \Exception("content is not encrypted for this user " . $userId . " | ge-id: " . $groupElementId);
        }

        return current($res);
    }

    /**
     * OBSOLETE
     *
     * returns the existing intermediate keys for given user and groupElement
     *
     * @param int $userId
     * @param int $groupElementId
     *
     * @return IntermediateKey
     */
    public function getMatchingIntermediateKey(int $userId, int $groupElementId): object
    {
        $query = $this->createQuery();

        return $query->matching(
            $query->logicalAnd([
                $query->equals('user', $userId),
                $query->equals('groupElement', $groupElementId)
            ])
        )
            ->execute()
            ->getFirst();
    }
}
