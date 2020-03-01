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

use Neos\Flow\Annotations as Flow;
use TeamPass\Core\Domain\Dto\Element;
use TeamPass\Core\Domain\Model\GroupElement;
use TeamPass\Core\Domain\Model\GroupTreeElement;
use TeamPass\Core\Domain\Model\ElementTemplate;
use TeamPass\Core\Domain\Model\IntermediateKey;
use TeamPass\Core\Domain\Model\EncryptedContent;

/**
 * Class GroupElementService
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

class GroupElementService extends AbstractService
{
    /**
     * default placeholder is getting used for all fields which are encrypted and therefore not readable in grid
     *
     * @var string
     */
    protected const DEFAULT_PLACEHOLDER = "********";

    /**
     * creates a new group element with given name
     *
     * @param Element $element
     *
     * @return int
     * @throws \Exception
     */
    public function createGroupElement(Element $element): int
    {
        $groupTreeElement = $this->groupTreeElementRepository->load($element->getGroupId());

        $groupElement = new GroupElement();
        $groupElement->setName($element->getTitle());
        $groupElement->setGroupTreeElement($groupTreeElement);

        $this->groupElementRepository->add($groupElement);
        $this->persistenceManager->persistAll();

        return $groupElement->getId();
    }

    /**
     * Updates a group element
     *
     * @param integer $userId  the user id
     * @param Element $element the element dto
     *
     * @return array
     * @throws \Exception
     */
    public function updateGroupElement(int $userId, Element $element): array
    {
        // the elementId property in DTO has to have be a sting because of extjs 6
        $elementId = (int) $element->getElementId();
        /** @var GroupElement $groupElement */
        $groupElement = $this->groupElementRepository->load($elementId);

        if ($element->getTitle() !== null) {
            $groupElement->setName($element->getTitle());
        }
        if ($element->getComment() !== null) {
            $groupElement->setComment($element->getComment());
        }

        $this->groupElementRepository->update($groupElement);

        $result = array();

        if ($groupElement->getEncryptedContents()->count() === 0) {
            $result["isEncrypted"] = false;
        } else {
            $result["isEncrypted"] = true;
            $intermediateKey = $this->intermediateKeyRepository->getIntermediateKeyForUser(
                $userId,
                $groupElement->getId()
            );
            $result["rsaEncAesKey"] = $intermediateKey->getRsaEncryptedAesKey();
        }

        return $result;
    }

    /**
     * Deletes a group element
     *
     * @param int $elementId the group element id
     *
     * @return void
     * @throws \Exception
     */
    public function deleteGroupElement(int $elementId): void
    {
        /** @var GroupElement $groupElement */
        $groupElement = $this->groupElementRepository->load($elementId);

        $this->groupElementRepository->update($groupElement);

        /** @var EncryptedContent $ec */
        foreach ($groupElement->getEncryptedContents() as $ec) {
            $this->encryptedContentRepository->remove($ec);
        }

        /** @var IntermediateKey $ik */
        foreach ($groupElement->getIntermediateKeys() as $ik) {
            $this->intermediateKeyRepository->remove($ik);
        }
        $this->groupElementRepository->remove($groupElement);
    }

    /**
     * Return all group elements for given group tree element
     *
     * @param int $userId  the user id
     * @param int $groupId the group id
     *
     * @return array
     * @throws \Exception
     */
    public function getAllElements(int $userId, int $groupId): array
    {
        /** @var GroupTreeElement $group */
        $group = $this->groupTreeElementRepository->load($groupId);

        $elements = $group->getElements();

        $result = array();

        /** @var GroupElement $element */
        foreach ($elements as $element) {
            $tmp = array();
            $tmp["title"] = $element->getName();
            $tmp["elementId"] = $element->getId();
            $tmp["comment"] = $element->getComment();
            $tmp["groupId"] = $element->getGroupTreeElement()->getGroupTreeElementId();

            if ($element->getEncryptedContents()->count() === 0) {
                /** @var ElementTemplate $template*/
                $template = $this->elementTemplateRepository->findOneByInternalName(ElementTemplate::DEFAULT_TEMPLATE);

                $tmp["template"] = $template->getInternalName();
                $tmp["isEncrypted"] = false;
            } else {
                $tmp["isEncrypted"] = true;
                $tmp["template"] = $element->getCurrentEncryptedEntity()->getTemplate()->getInternalName();

                try {
                    $intermediateKey = $this->intermediateKeyRepository->getIntermediateKeyForUser(
                        $userId,
                        $element->getId()
                    );
                } catch (\Exception $e) {
                    $tmp["rsaEncAesKey"] = false;
                    $result[] = $tmp;
                    $this->logger->debug($e->getMessage() . $e->getTraceAsString());
                    continue;
                }

                $tmp["rsaEncAesKey"] = $intermediateKey->getRsaEncryptedAesKey();
            }
            $result[] = $tmp;
        }
        return $result;
    }
}
