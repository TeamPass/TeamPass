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
use Neos\Flow\Mvc\Controller\RestController;
use Neos\Flow\Session\SessionInterface;
use TeamPass\Core\Exception\ParameterValidationException;
use TeamPass\ApiV1\Service\TranslatorService;
use TeamPass\Core\Property\TypeConverter\TeamPassDtoConverter;

/**
 * Class AbstractController
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */
abstract class AbstractController extends RestController
{
    /**
     * @var array
     */
    protected $supportedMediaTypes = ['application/json'];

    /**
     * @var array
     */
    protected $viewFormatToObjectNameMap = [
        'json' =>  \Neos\Flow\Mvc\View\JsonView::class
    ];

    /**
     * @var int
     */
    protected const RANDOMKEYLENGTH = 24;

    /**
     * @Flow\Inject
     * @var TranslatorService
     */
    protected $translatorService;

    /**
     * @Flow\Inject
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var array
     */
    protected $postParams;

    /**
     * @var array
     */
    protected $getParams;

    /**
     * alternative constructor
     *
     * @return void
     * @throws \Neos\Flow\Session\Exception\SessionNotStartedException
     */
    protected function initializeAction(): void
    {
        // throw exception when user is not logged in
        if ($this->session->isStarted() && $this->session->getData("language")) {
            $lang = (string) $this->session->getData("language");
            $this->translatorService->setLocale($lang);
        }

        $this->postParams = $this->request->getHttpRequest()->getBody()->getContents();
        $this->getParams = $this->request->getHttpRequest()->getQueryParams();
    }

    /**
     * A special action which is called if the originally intended action could
     * not be called, for example if the arguments were not valid.
     *
     * @return string
     * @api
     */
    protected function errorAction()
    {
        $msg = trim($this->getFlattenedValidationErrorMessage());

        $this->response->setStatusCode(400);

        return json_encode(["success" => false, "message" => $msg]);
    }

    /**
     * validate if given value is boolean.
     *
     * @param mixed $value value which should be boolean
     *
     * @return boolean
     * @throws ParameterValidationException
     */
    protected function validateBoolean($value): bool
    {
        if (!is_bool($value)) {
            throw new ParameterValidationException("Validation Error: '{$value}' is not boolean");
        }
        return $value;
    }

    /**
     * validate if given value is a integer or at least numeric
     *
     * @param mixed $value value which sould be a integer/numeric
     *
     * @return int
     * @throws ParameterValidationException
     */
    protected function validateInteger($value): int
    {
        if (!is_numeric($value)) {
            throw new ParameterValidationException("Validation Error: '{$value}' is not numeric");
        }
        return (int)$value;
    }

    /**
     * validate if given value is a string
     *
     * @param mixed $value value which should be a string
     *
     * @return string
     * @throws ParameterValidationException
     */
    protected function validateString($value): string
    {
        if (!is_string($value)) {
            throw new ParameterValidationException("Validation Error: '{$value}' is not a string");
        }
        return $value;
    }

    /**
     * validate if given value is a array
     *
     * @param mixed $value value which should be a array
     *
     * @return array
     * @throws ParameterValidationException
     */
    protected function validateArray($value): array
    {
        if (!is_array($value)) {
            throw new ParameterValidationException("Validation Error: is not a array");
        }
        return $value;
    }

    /**
     * generates a new salt and returns it
     *
     * @param int $length random key length in digits
     *
     * @return string
     */
    protected function generateRandomKey(int $length = self::RANDOMKEYLENGTH): string
    {
        try {
            $randomBytes = random_bytes(128);
        } catch (\Exception $e) {
            $randomBytes = openssl_random_pseudo_bytes(128);
        }

        $randomBytesInHex = bin2hex($randomBytes);

        return substr($randomBytesInHex, 0, $length);
    }

    /**
     *
     *
     * @param string $argument
     * @param array $properties
     *
     * @return void
     * @throws \Neos\Flow\Mvc\Exception\NoSuchArgumentException
     */
    protected function abstractInitialize(string $argument, array $properties): void
    {
        $propertyMappingConfiguration = $this->arguments->getArgument($argument)->getPropertyMappingConfiguration();

        if (empty($properties)) {
            $propertyMappingConfiguration->allowAllProperties();
        } else {
            $propertyMappingConfiguration->allowProperties(...$properties);
        }

        $propertyMappingConfiguration->setTypeConverter(new TeamPassDtoConverter());
    }
}
