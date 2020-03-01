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

namespace TeamPass\ApiV1\Controller;

use Neos\Flow\Annotations as Flow;
use TeamPass\Core\Exception\InvalidSessionHttpException;
use TeamPass\ApiV1\Service\AclService;

/**
 * Class ProtectedAdminController
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

abstract class ProtectedAdminController extends ProtectedController
{
    /**
     * @Flow\Inject
     * @var AclService
     */
    protected $aclService;

    /**
     * @throws \Exception
     */
    protected function initializeAction(): void
    {
        try {
            parent::initializeAction();

            $userId = (int) $this->session->getData("userId");

            $this->aclService->isAdmin($userId);
        } catch (InvalidSessionHttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            // log the exception message
            $this->logger->error($e->getMessage());

            throw new InvalidSessionHttpException("Permission denied");
        }
    }
}
