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

/**
 * Class ExportWriterService
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2022 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

class ExportWriterService extends AbstractService
{
    /**
     * @var string
     */
    const SEPARATOR = ";";

    /**
     * @var string
     */
    const ENCLOSURE = '"';

    /**
     * @var array
     */
    const DEFAULT_HEADER = [
        "collections",
        "type",
        "name",
        "notes",
        "login_uri",
        "login_username",
        "login_password",
        "login_totp"
    ];

    /**
     * @param $relativePath
     * @param $targetFileName
     * @param $data
     * @return void
     */
    public function process($relativePath, $targetFileName, $data)
    {
        $handler = $this->createFile($relativePath, $targetFileName);
        $processedData = $this->processData($data);
        $this->writeData($handler, $processedData);
    }

    /**
     * @param $relativePath
     * @param $targetFileName
     * @return false|resource
     */
    protected function createFile($relativePath, $targetFileName)
    {
        $path = FLOW_PATH_DATA . DIRECTORY_SEPARATOR . $relativePath . DIRECTORY_SEPARATOR . $targetFileName;
        return fopen($path, 'w+');
    }

    /**
     * @param $data
     * @return array
     */
    protected function processData($data)
    {
        $processedData = [];

        foreach ($data as $row) {
            if (is_array($row['elements'])) {
                foreach($row['elements'] as $element) {
                    $type="note";
                    if (!isset($element['name'])) {
                        $element['name'] = "";
                    }
                    if (!isset($element['content']['url'])) {
                        $element['content']['url'] = "";
                    }
                    if (!isset($element['content']['password'])) {
                        $element['content']['password'] = "";
                    }
                    if (!isset($element['content']['username'])) {
                        $element['content']['username'] = "";
                    }
                    if (!isset($element['content']['title'])) {
                        $element['content']['title'] = "";
                    }
                    if (!isset($element['content']['rtecontent'])) {
                        $element['content']['rtecontent'] = "";
                        $type="login";
                    }

                    $element['content']['rtecontent'] = strip_tags($element['content']['rtecontent']);
                    $element['content']['rtecontent']  = str_replace("\n" , '', $element['content']['rtecontent'] );

                    if (strlen($element['content']['rtecontent']) > 6000) {
                        continue;
                    }

                    $tmp = [];
                    $tmp["collections"] = implode($row['path'], "/");
                    $tmp["type"] = $type;
                    $tmp["name"] = $element["name"];

                    if ($type === "login") {
                        $tmp["notes"] =$element['comment'];
                    } else {
                        $tmp["notes"] = $element['content']['rtecontent'];
                    }

                    $tmp["login_uri"] = $element['content']['url'];
                    $tmp["login_username"] =$element['content']['username'];
                    $tmp["login_password"] =$element['content']['password'];
                    $tmp["login_totp"] = "";


                    $processedData[] = $tmp;
                }
            }
        }
        return $processedData;
    }

    protected function writeData($handler, $processedData)
    {
        $this->writeHeader($handler);

        foreach ($processedData as $row) {
                fputcsv($handler, $row, self::SEPARATOR, self::ENCLOSURE);
        }
    }

    protected function writeHeader($handler)
    {
        //write header
        fputcsv($handler, self::DEFAULT_HEADER, self::SEPARATOR, self::ENCLOSURE);
    }
}
