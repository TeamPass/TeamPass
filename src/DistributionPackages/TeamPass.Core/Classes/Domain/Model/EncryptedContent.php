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

namespace TeamPass\Core\Domain\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

/**
 * Class EncryptedContent
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 *
 * @Flow\Entity
 */
class EncryptedContent
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var int
     */
    protected $timeStamp;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    protected $content;

    /**
     * @var GroupElement
     * @ORM\ManyToOne(inversedBy="encryptedContents")
     * ORM\JoinColumn(onDelete="CASCADE")
     **/
    protected $groupElement;

    /**
     * @var ElementTemplate
     * @ORM\ManyToOne(inversedBy="encryptedContents")
     **/
    protected $template;

    /**
     * returns entity id
     *
     * @return int
     */
    public function getEncryptedId(): int
    {
        return $this->id;
    }

    /**
     * Returns the value of the class member content
     *
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Sets the value for the class member _content.
     *
     * @param string $content Holds the value for the class _content
     *
     * @return $this
     */
    public function setContent($content): EncryptedContent
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Set App\Core\Entities\GroupElement
     *
     * @param GroupElement $groupElement the group element instance
     *
     * @return $this
     */
    public function setGroupElement(GroupElement $groupElement): EncryptedContent
    {
        $this->groupElement = $groupElement;
        return $this;
    }

    /**
     * get App\Core\Entities\GroupElement
     *
     * @return ArrayCollection
     */
    public function getGroupElement(): object
    {
        return $this->groupElement;
    }

    /**
     * sets template Object
     *
     * @param ElementTemplate $template the element template instance
     *
     * @return $this
     */
    public function setTemplate(ElementTemplate $template): EncryptedContent
    {
        $this->template = $template;
        return $this;
    }

    /**
     * returns template object
     *
     * @return ElementTemplate
     */
    public function getTemplate(): ElementTemplate
    {
        return $this->template;
    }
}
