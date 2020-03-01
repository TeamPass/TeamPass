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

namespace TeamPass\Core\Util;

/**
 * Class Keys
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

class Keys
{
    /**
     * the default application language
     *
     * @var string
     */
    public const DEFAULT_LANGUAGE = "en";

    /**
     * the default application theme
     *
     * @var string
     */
    public const DEFAULT_THEME = "teampass";

    /**
     * pink application theme
     *
     * @var string
     */
    public const PINK_THEME = "teampasspink";

    /**
     * poll interval in seconds
     *
     * @var integer
     */
    public const DEFAULT_POLL_INTERVAL = 10;
}
