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

namespace TeamPass\Core\Factory;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Reflection\ReflectionService;
use Psr\Log\LoggerInterface;
use TeamPass\Core\Adapter\Model\Configuration;
use TeamPass\Core\Exception\AdapterException;
use TeamPass\Core\Interfaces\AdapterImplementationClassInterface;
use TeamPass\Core\Interfaces\AdapterInterface;

/**
 * Class AdapterFactory
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

class AdapterFactory extends AbstractFactory
{
    /**
     * Basic namespace for all authentication adapters
     *
     * @var string
     */
    protected const ADAPTER_NAMESPACE = 'TeamPass\\Core\\Adapter\\';

    /**
     * @Flow\Inject
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @Flow\Inject
     *
     * @var ReflectionService
     */
    protected $reflectionService;
    
    /**
     * @Flow\Inject
     *
     * @var ImplementationClassFactory
     */
    protected $implementationClassFactory;

    /**
     * creates a new instance of given adapter class
     *
     * @param string $class  the class name
     * @param array  $config the configuration
     *
     * @return AdapterInterface
     * @throws \Exception
     */
    public function create(string $class, array $config): object
    {
        $implementationClass = $this->implementationClassFactory->create($class);
        $configuration = $this->createConfiguration($config, $implementationClass);

        $adapter = $this->createAdapterClass($implementationClass, $configuration);

        return $adapter;
    }

    /**
     * creates a adapter class instance based on given implementation class object
     *
     * @param AdapterImplementationClassInterface $implementationClass the implementation class object
     * @param Configuration                       $config              the configuration instance
     *
     * @return mixed
     * @throws AdapterException
     */
    protected function createAdapterClass(
        AdapterImplementationClassInterface $implementationClass,
        Configuration $config
    ): object {
        $adapterClassName = $implementationClass->getAdapter();

        $fullClass = self::ADAPTER_NAMESPACE . $adapterClassName;

        if ($this->reflectionService->isClassImplementationOf($fullClass, AdapterInterface::class)) {
            /** @var AdapterInterface $class */
            $class = new $fullClass();
            $class->setImplementationClass($implementationClass);
            $class->setConfiguration($config);

            return $class;
        }
        throw new AdapterException("Adapter '{$fullClass}' don't exist or not implements required interface");
    }

    /**
     * creates a configuration model for given configuration array
     *
     * @param array                               $config              the adapter configuration
     * @param AdapterImplementationClassInterface $implementationClass the implementation class object
     *
     * @return Configuration
     */
    protected function createConfiguration(
        array $config,
        AdapterImplementationClassInterface $implementationClass
    ): object {
        return new Configuration($config, $implementationClass);
    }
}
