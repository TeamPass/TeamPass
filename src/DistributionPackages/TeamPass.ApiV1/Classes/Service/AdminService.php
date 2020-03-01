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

use TeamPass\Core\Domain\Model\Acl;
use TeamPass\Core\Domain\Model\GroupTreeElement;
use Neos\Flow\Annotations as Flow;

/**
 * Class AdminService
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

class AdminService extends AbstractService
{
    /**
     * Holds Ids and indexes to find find entity with same index
     *
     * @var array
     */
    protected $cache = array();

    /**
     * @Flow\Inject
     * @var EncryptionService
     */
    protected $encryptionService;

    /**
     * Returns nodes as tree
     *
     * @return array
     */
    public function getNodesAsTree(): array
    {
        /** @var array $list */
        $list = $this->groupTreeElementRepository->findAll();

        $ar = array();

        /** @var GroupTreeElement $row */
        foreach ($list as $row) {
            if (!$row->getLeaf()) {
                if (!$row->getParent()) {
                    $parentId = 0;
                } else {
                    $parentId = $row->getParent()->getGroupTreeElementId();
                }

                $ar[$parentId][] = array(
                        "text" => $row->getName(),
                        "parentId" => $parentId,
                        "expanded" => true,
                        "leaf" => false,
                        "id" => $row->getGroupTreeElementId(),
                    );
            }
        }
        $res = $this->recursiveTreeBuild($ar, $ar[0]);

        return $this->addRootNode($res);
    }

    /**
     *
     *
     * @param int $gteId the group tree element id
     *
     * @return array
     * @throws \Exception
     */
    public function getPermissionsForGroupTreeElement(int $gteId): array
    {
        /** @var GroupTreeElement $groupTreeElement */
        $groupTreeElement = $this->groupTreeElementRepository->load($gteId);

        if ($groupTreeElement->getLeaf()) {
            throw new \Exception("leaf group tree element can not hold permissions");
        }

        $result = [];
        /** @var Acl $acl */
        foreach ($groupTreeElement->getAcls() as $acl) {
            if (!$acl->getGroup()->isAdmin()) {
                $result[] = array(
                    "groupName" => $acl->getGroup()->getName(),
                    "pRead" => $acl->getRead(),
                    "pCreate" => $acl->getCreate(),
                    "pUpdate" => $acl->getUpdate(),
                    "pDelete" => $acl->getDelete(),
                    "inherited" => $acl->getInherited(),
                    "gteId" => $gteId,
                    "userGroupId" => $acl->getGroup()->getUserGroupId(),
                    "id" => $acl->getAclId()
                );
            }
        }

        return $result;
    }

    /**
     * adds a root node to node tree array
     *
     * @param array $res node tree
     *
     * @return array
     */
    protected function addRootNode(array $res): array
    {
        return array("expanded" => true,
            "id" => "root",
            "index" => 0,
            "leaf" => false,
            "parentId" => null,
            "text" => "Root",
            "children" => $res
        );
    }

    /**
     * Helper to build tree
     *
     * @param array $ar    node tree array
     * @param array $curar current node tree array
     *
     * @return array
     */
    protected function recursiveTreeBuild(array $ar, array $curar): array
    {
        $i = 0;
        $result = array();

        foreach ($curar as $row) {
            if (isset($row["expanded"]) && $row["expanded"] == 1) {
                $row["expanded"] = true;
            } else {
                $row["expanded"] = false;
            }
            if ($row["leaf"] != 1) {
                $row["leaf"] = false;
            } else {
                $row["leaf"] = true;
            }
            if ($row["parentId"] === 0) {
                $row["parentId"] = "root";
            }

            $result[$i] = $row;

            $result[$i]['children'] = array();
            if (isset($row['id'])) {
                if (isset($ar[$row['id']])) {
                    $children = $this->recursiveTreeBuild($ar, $ar[$row['id']]);
                    $result[$i]['children'] = $children;
                }
            }
            $i++;
        }
        return $result;
    }
}
