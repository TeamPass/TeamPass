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
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * Class IntermediateKey
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 *
 * @ORM\Table(uniqueConstraints={@UniqueConstraint(name="user_groupElement_idx", columns={"user", "groupelement"})})
 * @Flow\Entity
 */
class IntermediateKey
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
     * @ORM\Column(type="text")
     * @var string
     */
    protected $encryptedAesKey = "";

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    protected $rsaEncryptedAesKey = "";

    /**
     * @var User
     * @ORM\ManyToOne(inversedBy="intermediateKey")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(inversedBy="intermediateKeys")
     * @var GroupElement
     */
    protected $groupElement;

    /**
     * Sets the encrypted aes key
     *
     * @param string $encryptedAesKey the encrypted aes key
     *
     * @return $this
     */
    public function setEncryptedAesKey(string $encryptedAesKey): IntermediateKey
    {
        $this->encryptedAesKey = $encryptedAesKey;
        return $this;
    }

    /**
     * Returns encrypted aes key
     *
     * @return string
     */
    public function getEncryptedAesKey(): string
    {
        return $this->encryptedAesKey;
    }

    /**
     * Returns all groupElements
     *
     * @return GroupElement
     */
    public function getGroupElement(): GroupElement
    {
        return $this->groupElement;
    }

    /**
     * Returns entity id
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Sets the rsa encrypted aes key
     *
     * @param string $rsaEncryptedAesKey the rsa encrypted aes key
     *
     * @return $this
     */
    public function setRsaEncryptedAesKey(string $rsaEncryptedAesKey): IntermediateKey
    {
        $this->rsaEncryptedAesKey = $rsaEncryptedAesKey;
        return $this;
    }

    /**
     * Returns the rsa encrypted aes key
     *
     * @return string
     */
    public function getRsaEncryptedAesKey(): string
    {
        return $this->rsaEncryptedAesKey;
    }

    /**
     * Sets the user instance
     *
     * @param User $user the user instance
     *
     * @return $this
     */
    public function setUser(User $user): IntermediateKey
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Returns the user instance
     *
     * @return ArrayCollection
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Sets the group element
     *
     * @param GroupElement $groupElement the group element instance
     *
     * @return $this
     */
    public function setGroupElement(GroupElement $groupElement): IntermediateKey
    {
        $this->groupElement = $groupElement;
        return $this;
    }
}
