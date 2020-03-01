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

/**
 * Class AbstractFactory
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

abstract class AbstractFactory
{
    /**
     * checks if given class implements given interface
     *
     * @param string $className the full class name with declare(strict_types=1);

namespace
     * @param string $interface interface with declare(strict_types=1);

namespace
     *
     * @return bool true if given class implements given interface, false if not
     */
    protected function classImplements(string $className, string $interface): bool
    {
        $result = class_implements($className);
        if (!is_array($result)) {
            return false;
        }

        if (!in_array($interface, $result)) {
            return false;
        }

        return true;
    }
}
