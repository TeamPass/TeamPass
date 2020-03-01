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
 * Class Element
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */
class Element
{
    /**
     * @var string
     */
    protected $encryptedContent;

    /**
     * @var string
     * @Flow\Validate(type="NotEmpty", validationGroups={"EncryptionControllerUpdateElementAction"})
     */
    protected $decryptedContent;

    /**
     * @var string
     * @Flow\Validate(type="NotEmpty", validationGroups={"EncryptionControllerUpdateElementAction"})
     * @Flow\Validate(type="NotEmpty", validationGroups={"GroupElementControllerCreateAction"})
     */
    protected $template;

    /**
     * @var mixed
     */
    protected $encAesKey;

    /**
     * @var string
     * @Flow\Validate(type="NotEmpty", validationGroups={"EncryptionControllerGetEncryptedElementAction"})
     */
    protected $aesKey;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var int
     * @Flow\Validate(type="NotEmpty", validationGroups={"GroupElementControllerCreateAction"})
     */
    protected $groupId;

    /**
     * @var int|null
     * @Flow\Validate(type="NotEmpty", validationGroups={"EncryptionControllerGetEncryptedElementAction"})
     * @Flow\Validate(type="NotEmpty", validationGroups={"GroupElementControllerUpdateAction"})
     */
    protected $elementId;

    /**
     * @var string
     */
    protected $comment;

    /**
     * @var string
     */
    protected $rsaEncAesKey;

    /**
     * @var bool
     * @Flow\Validate(type="NotEmpty", validationGroups={"GroupElementControllerCreateAction"})
     */
    protected $isEncrypted;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->elementId;
    }

    /**
     * @param string $id
     */
    public function setId($id): void
    {
        $this->setElementId($id);
    }

    /**
     * @return string
     */
    public function getEncryptedContent(): string
    {
        return $this->encryptedContent;
    }

    /**
     * @param string $encryptedContent
     */
    public function setEncryptedContent(string $encryptedContent): void
    {
        $this->encryptedContent = $encryptedContent;
    }

    /**
     * @return string
     */
    public function getTemplateName(): string
    {
        return $this->template;
    }

    /**
     * @param string $templateName
     */
    public function setTemplateName(string $templateName): void
    {
        $this->template = $templateName;
    }

    /**
     * @return bool|string
     */
    public function getEncAesKey()
    {
        return $this->encAesKey;
    }

    /**
     * @param bool|string $encAesKey
     */
    public function setEncAesKey($encAesKey): void
    {
        $this->encAesKey = $encAesKey;
    }

    /**
     * @return bool|string
     */
    public function getAesKey()
    {
        return $this->aesKey;
    }

    /**
     * @param string $aesKey
     */
    public function setAesKey($aesKey): void
    {
        $this->aesKey = $aesKey;
    }

    /**
     * @return string
     */
    public function getDecryptedContent(): string
    {
        return $this->decryptedContent;
    }

    /**
     * @param string $decryptedContent
     */
    public function setDecryptedContent(string $decryptedContent): void
    {
        $this->decryptedContent = $decryptedContent;
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @param string $template
     */
    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return int
     */
    public function getGroupId(): int
    {
        return $this->groupId;
    }

    /**
     * @param int $groupId
     */
    public function setGroupId(int $groupId): void
    {
        $this->groupId = $groupId;
    }

    /**
     * @return int
     */
    public function getElementId(): ?int
    {
        return $this->elementId;
    }

    /**
     * @param string $elementId
     */
    public function setElementId($elementId): void
    {
        if (is_numeric($elementId)) {
            $this->elementId = (int) $elementId;
        } else {
            $this->elementId = null;
        }
    }

    /**
     * @return string
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @return string
     */
    public function getRsaEncAesKey(): string
    {
        return $this->rsaEncAesKey;
    }

    /**
     * @param string $rsaEncAesKey
     */
    public function setRsaEncAesKey(string $rsaEncAesKey): void
    {
        $this->rsaEncAesKey = $rsaEncAesKey;
    }

    /**
     * @return bool
     */
    public function getisEncrypted(): bool
    {
        return $this->isEncrypted;
    }

    /**
     * @param bool $isEncrypted
     */
    public function setIsEncrypted(bool $isEncrypted): void
    {
        $this->isEncrypted = $isEncrypted;
    }
}
