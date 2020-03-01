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
use Neos\Flow\I18n\Locale;
use Neos\Flow\I18n\Translator;

/**
 * Class TranslatorService
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

class TranslatorService
{
    /**
     * @var string
     */
    protected const SOURCE_NAME = "Main";

    /**
     * @var string
     */
    protected const PACKAGE_KEY = "TeamPass.ApiV1";

    /**
     * The translator instance
     *
     * @Flow\Inject
     * @var Translator
     */
    protected $translator;

    /**
     * @var string
     */
    protected $locale;

    /**
     * sets the locale
     *
     * @param string $locale
     *
     * @return void
     */
    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    /**
     * returns the locale
     *
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * translates given id
     *
     * @param string      $id         the message id
     * @param array       $parameters translation parameters
     * @param string|null $quantity   the quantity
     * @param string|null $locale     The locale or null to use the default
     *
     * @return string
     */
    public function trans(string $id, ?array $parameters = array(), ?string $quantity = null, ?string $locale = null)
    {
        // is no locale was set use the default one
        if ($locale === null) {
            $locale = $this->locale;
        }

        $locale = new Locale("en");

        return $this->translator->translateById(
            $id,
            $parameters,
            $quantity,
            $locale,
            self::SOURCE_NAME,
            self::PACKAGE_KEY
        );
    }
}
