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

namespace TeamPass\Core\Adapter;

use Neos\Flow\Annotations as Flow;
use Adldap\Connections\ProviderInterface;
use Psr\Log\LoggerInterface;
use TeamPass\Core\Adapter\Model\Configuration;
use TeamPass\Core\Domain\Dto\Person;
use TeamPass\Core\Exception\Backend\LoginFailedException;
use TeamPass\Core\Exception\Backend\UserNotFoundException;
use TeamPass\Core\Interfaces\AdapterImplementationClassInterface;
use TeamPass\Core\Interfaces\AdapterInterface;
use TeamPass\ApiV1\Service\AuthService;

/**
 * Class LdapAdapter
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

class LdapAdapter implements AdapterInterface
{
    /**
     * @Flow\Inject
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var AdapterImplementationClassInterface
     */
    protected $implementationClass;

    /**
     * @var array
     */
    protected $adLdapConfig;

    /**
     * @var ProviderInterface
     */
    protected $provider;

    /**
     * sets the configuration
     *
     * @param Configuration $configuration the configuration model
     *
     * @return void
     *
     * @throws \Exception
     */
    public function setConfiguration(Configuration $configuration): void
    {
        $this->configuration = $configuration;

        $config = [
            'hosts'                 => array($this->configuration->get("hostname")),
            'base_dn'               => $this->configuration->get("ldapBasedn"),
            'username'              => $this->configuration->get("ldapAdminDn"),
            'password'              => $this->configuration->get("ldapAdminPassword"),
            'port'                  => $this->configuration->get("port"),
            'schema'                => \Adldap\Schemas\OpenLDAP::class,
            'follow_referrals'      => false,
            'use_ssl'               => false,
            'use_tls'               => false
        ];

        $security = $this->configuration->get("security");

        if ($security === "ssl") {
            $config['use_ssl'] = true;
        } elseif ($security === "tls") {
            $config['use_tls'] = true;
        }

        $this->adLdapConfig = $config;
    }


    /**
     * sets the implementation class object
     *
     * @param AdapterImplementationClassInterface $implementationClass the implementation class object
     *
     * @return void
     */
    public function setImplementationClass(AdapterImplementationClassInterface $implementationClass): void
    {
        $this->implementationClass = $implementationClass;
    }

    /**
     * initial Setup for ldap connection
     *
     * @return void
     */
    public function setup(): void
    {
        $ad = new \Adldap\Adldap();

        $ad->addProvider($this->adLdapConfig);
        $this->provider = $ad->connect();
    }

    /**
     * Try to authenticate with given credentials
     *
     * @param Person $person the person value object
     *
     * @return void
     *
     * @throws LoginFailedException
     * @throws UserNotFoundException
     * @throws \Exception
     */
    public function auth(Person $person): void
    {
        try {
            $usernameField = $this->configuration->get("ldapUserUsername");
            $user = $this->provider
                ->search()
                ->select([$usernameField])
                ->whereEquals($usernameField, $person->getUsername())
                ->firstOrFail();

            $this->provider->auth()->bind($user->dn[0], $person->getPassword());
        } catch (\Adldap\Auth\BindException $e) {
            // do not log exception! can contain username and password of user
            throw new LoginFailedException("login failed on backend. Message was: {$e->getMessage()}");
        } catch (\Adldap\Models\ModelNotFoundException $e) {
            // do not log exception! can contain username and password of user
            throw new UserNotFoundException("user not available in backend. Message was: {$e->getMessage()}");
        }
    }

    /**
     * Fetch all users based on basic $userObjectFilter, Build an Array containing, Username, Email and FullName
     * and return it. The username is also used as array key for the multidimensional array. This method could be used
     * for periodic fetching/Updating User-Settings
     *
     * @param int $userLimit number of users to be returned
     *
     * @return array
     *
     * @throws \Exception
     */
    public function fetchUsers(int $userLimit = 0): array
    {
        try {
            $filter = $this->configuration->get("ldapUserFilter");
            $search = $this->provider->search()->rawFilter($filter);

            $requiredAttributes = array(
                $this->configuration->get("ldapUserUsername"),
                $this->configuration->get("ldapUserDisplayname"),
                $this->configuration->get("ldapUserEmail")
            );

            $select = $search->select($requiredAttributes)->limit($userLimit);
            $users = $select->get();

            $result = array();

            /** @var \Adldap\Models\User $user */
            foreach ($users as $user) {
                $tmp = array();
                $tmp[AuthService::USERNAME_ATTRIBUTE] = $user->getAttribute(
                    $this->configuration->get("ldapUserUsername")
                )[0];
                $tmp[AuthService::EMAIL_ATTRIBUTE] = $user->getAttribute($this->configuration->get("ldapUserEmail"))[0];
                $tmp[AuthService::FULLNAME_ATTRIBUTE] = $user->getAttribute(
                    $this->configuration->get("ldapUserDisplayname")
                )[0];

                if (
                    $tmp[AuthService::USERNAME_ATTRIBUTE] === null ||
                    $tmp[AuthService::EMAIL_ATTRIBUTE] === null ||
                    $tmp[AuthService::FULLNAME_ATTRIBUTE] === null
                ) {
                    continue;
                }

                $result[$tmp[AuthService::USERNAME_ATTRIBUTE]] = $tmp;
            }

            return $result;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage() . $e->getTraceAsString());
            throw new LoginFailedException("login failed on backend. Message was: {$e->getMessage()}");
        }
    }
}
