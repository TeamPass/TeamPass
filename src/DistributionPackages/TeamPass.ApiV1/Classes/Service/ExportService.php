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
use Neos\Flow\Persistence\Exception\IllegalObjectTypeException;
use TeamPass\Core\Domain\Model\EncryptedContent;
use TeamPass\Core\Domain\Model\GroupElement;
use TeamPass\Core\Domain\Model\GroupTreeElement;
use TeamPass\Core\Domain\Model\IntermediateKey;
use TeamPass\Core\Domain\Model\User;
use TeamPass\Core\Domain\Model\Acl;
use TeamPass\Core\Domain\Dto\TreeNode;
use TeamPass\Core\Exception\ExportException;
use TeamPass\Core\Exception\InvalidRequestException;

/**
 * Class ExportService
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2022 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

class ExportService extends AbstractService
{

    const exportDirectory = "export";

    /**
     * @var string
     */
    protected $targetFileName = "";

    /**
     * @var string
     */
    protected $fileExtension = "csv";

    /**
     * @var array
     */
    protected $basePath= [];

    /**
     * @Flow\Inject
     * @var GroupTreeService
     */
    protected $groupTreeService;

    /**
     * @Flow\Inject
     * @var ExportWriterService
     */
    protected $exportWriterService;

    /**
     * @var int
     */
    protected $userId;


    /**
     * @var \OpenSSLAsymmetricKey
     */
    protected $privateKey;

    /**
     * @param int $groupId
     * @param string $privateKey
     * @return string
     * @throws ExportException
     */
    public function process(int $userId, int $groupId, $privateKey)
    {
        $this->privateKey  = $privateKey;
        $this->userId = $userId;

        $this->prepareFilesystem();
        $this->generateTargetFileName($groupId);

        $data = $this->getChildrenWithElements($groupId);

        $this->exportWriterService->process(self::exportDirectory, $this->targetFileName, $data);

        return $this->targetFileName;
    }

    /**
     * @return array
     */
    protected function getChildrenWithElements(int $groupId)
    {
        $result = [];

        $basePath = $this->groupTreeService->getGroupNamePath($groupId);

        $childs = $this->recursiveGetChildrenWithElements($groupId, $basePath);

        return $childs;
    }

    protected function recursiveGetChildrenWithElements($groupId, $path, $data=[])
    {
        $children = $this->groupTreeElementRepository->getChilds($groupId);

        /** @var GroupTreeElement $child */
        foreach ($children as $child) {
            $newPath = $path;
            $newPath[] = $child->getName();

            if ($child->getLeaf()) {
                $tmp=[];
                $tmp["name"] = $child->getName();
                $tmp["path"] = $newPath;
                $tmp["elements"] = $this->getGroupElements($child);

                $data[] = $tmp;
            } else {
                $data = $this->recursiveGetChildrenWithElements($child->getGroupTreeElementId(), $newPath, $data);
            }
        }
        return $data;
    }

    protected function getGroupElements(GroupTreeElement $gte)
    {
        $data = [];
        /** @var GroupElement $element */
        foreach ($gte->getElements() as $element)
        {
            $tmp = [];
            $tmp['name'] = $element->getName();
            if ($element->getEncryptedContents()->last() === false) {
                $tmp['content'] = [];
            } else {
                $tmp['content'] = $this->decryptContent($element);
            }
            $tmp['comment'] = $element->getComment();

            $data[] = $tmp;
        }

        return $data;
    }

    protected function decryptContent(GroupElement $groupElement)
    {
        /** @var IntermediateKey $ik */
        $ik = $this->intermediateKeyRepository->getIntermediateKeyForUser($this->userId,  $groupElement->getId());
        $aesKey = $this->rsaDecrypt($ik->getRsaEncryptedAesKey(), $this->privateKey);
        $contentAesKeyJson = $this->aesDecrypt($ik->getEncryptedAesKey(), $aesKey);
        $contentAesKeyArray = json_decode($contentAesKeyJson,true);
        $contentAesKey = $contentAesKeyArray["key"];

        /** @var EncryptedContent $ec */
        $ec = $groupElement->getEncryptedContents()->last();

        $content = $this->aesDecrypt($ec->getContent(), $contentAesKey);
        return json_decode($content,true);
    }

    /**
     * @return void
     */
    protected function generateTargetFilename($groupId)
    {
        $this->targetFileName = $groupId . "_" . uniqid() . "." . $this->fileExtension;
    }

    /**
     * @return void
     * @throws ExportException
     */
    protected function prepareFilesystem()
    {
        if (!is_writable(FLOW_PATH_DATA)) {
            throw new ExportException("path '" . FLOW_PATH_DATA . "' is not writeable");
        }

        if (is_dir(FLOW_PATH_DATA . DIRECTORY_SEPARATOR . self::exportDirectory)) {
            if (!is_writable(FLOW_PATH_DATA . DIRECTORY_SEPARATOR . self::exportDirectory)) {
                throw new ExportException("path '" . FLOW_PATH_DATA . DIRECTORY_SEPARATOR . self::exportDirectory . "' is not writeable");
            }
            return;
        }

        if (!mkdir(FLOW_PATH_DATA . DIRECTORY_SEPARATOR . self::exportDirectory)){
            throw new ExportException("path '" . FLOW_PATH_DATA . DIRECTORY_SEPARATOR . self::exportDirectory . "' is not writeable");
        }
    }
}
