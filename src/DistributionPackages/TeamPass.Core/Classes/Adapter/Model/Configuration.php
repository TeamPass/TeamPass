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

namespace TeamPass\Core\Adapter\Model;

use TeamPass\Core\Interfaces\AdapterImplementationClassInterface;

/**
 * Class Configuration
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

class Configuration
{
    /**
     * @var array
     */
    protected $configuration;

    /**
     * @var AdapterImplementationClassInterface
     */
    protected $implementationClass;

    /**
     * the class constructor
     *
     * @param array                               $configuration       the configuration
     * @param AdapterImplementationClassInterface $implementationClass the implementation class
     */
    public function __construct(array $configuration, AdapterImplementationClassInterface $implementationClass)
    {
        $this->configuration = $configuration;
        $this->implementationClass = $implementationClass;
    }

    /**
     * returns the value for given configuration key
     *
     * @param string $key the config key
     *
     * @return string
     * @throws \Exception
     */
    public function get($key): string
    {
        if (isset($this->configuration[$key])) {
            return (string)$this->configuration[$key];
        }

        $presets = $this->implementationClass->getPresetValues();
        if (isset($presets[$key])) {
            return (string)$presets[$key];
        }

        throw new \Exception("configuration '{$key}' not found");
    }
}
