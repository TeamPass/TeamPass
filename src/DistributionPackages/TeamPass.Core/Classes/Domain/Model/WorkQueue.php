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

namespace TeamPass\Core\Domain\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

/**
 * Class WorkQueue
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 *
 * @Flow\Entity
 */
class WorkQueue
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @var integer
     */
    protected $id;

    /**
     * @ORM\OneToOne(inversedBy="workQueue")
     * @var User
     **/
    protected $user;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $action;

    /**
     * Returns entity id
     *
     * @return int
     */
    public function getWorkQueueId(): int
    {
        return $this->id;
    }

    /**
     * gets user entity
     *
     * @return User
     */
    public function getUser(): object
    {
        return $this->user;
    }

    /**
     * sets user entity
     *
     * @param User $user the user entity
     *
     * @return $this
     */
    public function setUser(User $user): object
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Sets the action for the work queue entity
     *
     * @param string $action the action which should be executed
     *
     * @return $this
     */
    public function setAction(string $action): object
    {
        $this->action = $action;
        return $this;
    }
}
