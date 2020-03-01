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
use TeamPass\Core\Exception\AdapterException;
use TeamPass\Core\Interfaces\AdapterImplementationClassInterface;

/**
 * Class ImplementationClassFactory
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

class ImplementationClassFactory extends AbstractFactory
{
    /**
     * Basic namespace for all implementation classes
     *
     * @var string
     */
    protected const IMPLEMENTATION_CLASS_NAMESPACE = 'TeamPass\\Core\\Adapter\\ImplementationClass\\';

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
     * creates a new instance of given adapter class
     *
     * @param string $class the class name
     *
     * @return AdapterImplementationClassInterface
     * @throws \Exception
     */
    public function create(string $class): object
    {
        return $this->createImplementationClass($class);
    }

    /**
     * creates a instance of the given implementation class
     *
     * @param string $class the implementation class classname
     *
     * @return mixed
     * @throws AdapterException
     */
    protected function createImplementationClass(string $class): object
    {
        $fullClass = self::IMPLEMENTATION_CLASS_NAMESPACE . $class;

        if ($this->reflectionService->isClassImplementationOf($fullClass, AdapterImplementationClassInterface::class)) {
            return new $fullClass();
        }

        throw new AdapterException("ImplementationClass '{$class}' don't exist or not implements required interface");
    }
}
