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

namespace TeamPass\Core\Domain\Dto;

use Neos\Flow\Annotations as Flow;

/**
 * Class TreeNode
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */
class TreeNode
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $parentId;

    /**
     * @var string
     */
    protected $text;

    /**
     * @var boolean
     */
    protected $expanded;

    /**
     * @var boolean
     */
    protected $leaf;

    /**
     * @var int
     */
    protected $index;

    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    /**
     * @param int $parentId
     */
    public function setParentId(int $parentId): void
    {
        $this->parentId = $parentId;
    }

    /**
     * @return string
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /**
     * @return bool
     */
    public function isExpanded(): ?bool
    {
        return $this->expanded;
    }

    /**
     * @param bool $expanded
     */
    public function setExpanded(bool $expanded): void
    {
        $this->expanded = $expanded;
    }

    /**
     * @return int
     */
    public function getIndex(): ?int
    {
        return $this->index;
    }

    /**
     * @param int $index
     */
    public function setIndex(int $index): void
    {
        $this->index = $index;
    }

    /**
     * @return bool
     */
    public function isLeaf(): bool
    {
        return $this->leaf;
    }

    /**
     * @param bool $leaf
     */
    public function setLeaf(bool $leaf): void
    {
        $this->leaf = $leaf;
    }
}
