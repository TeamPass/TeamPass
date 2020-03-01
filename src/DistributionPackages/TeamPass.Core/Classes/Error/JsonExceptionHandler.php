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

namespace TeamPass\Core\Error;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Error\AbstractExceptionHandler;
use Neos\Flow\Error\WithHttpStatusInterface;
use Neos\Flow\Error\WithReferenceCodeInterface;
use Neos\Flow\Http\Helper\ResponseInformationHelper;
use TeamPass\Core\Exception\InvalidSessionHttpException;

/**
 * Class JsonExceptionHandler
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 *
 * @Flow\Scope("singleton")
 */

class JsonExceptionHandler extends AbstractExceptionHandler
{
    /**
     * Echoes an json_encoded exception for the web
     *
     * @param \Throwable $exception
     * @return void
     */
    protected function echoExceptionWeb($exception)
    {
        $statusCode = ($exception instanceof WithHttpStatusInterface) ? $exception->getStatusCode() : 500;
        $statusMessage = ResponseInformationHelper::getStatusMessageByCode($statusCode);
        $referenceCode = ($exception instanceof WithReferenceCodeInterface) ? $exception->getReferenceCode() : null;
        if (!headers_sent()) {
            header(sprintf('HTTP/1.1 %s %s', $statusCode, $statusMessage));
        }

        $result['success'] = false;
        $result["message"] = $exception->getMessage();
        if ($referenceCode) {
            $result["code"] = $referenceCode;
        }

        if ($exception instanceof InvalidSessionHttpException) {
            $result['actions']['action'] = "logout";
        }

        echo json_encode($result);
    }
}
