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
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;
use TeamPass\Core\Domain\Dto\Person;

/**
 * Class User
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 *
 * @Flow\Entity
 */
class User
{
    /**
     * saltLength for UserPassword
     *
     * @var int
     */
    protected const SALTLENGTH = 24;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @var integer
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $email;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $username;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $password;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $language;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string|null
     */
    protected $theme;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @var bool
     */
    protected $alphabeticalOrder;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $fullName;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    protected $privateKey;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $rsaSalt;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    protected $publicKey;

    /**
     * @ORM\Column(type="boolean")
     * @var boolean
     */
    protected $enabled;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    protected $treeSettings;

    /**
     * Flag if user is coming from a external directory an deleted on this side
     *
     * @ORM\Column(type="boolean", nullable=true)
     * @var boolean
     */
    protected $deleted = false;

    /**
     * @ORM\ManyToOne(inversedBy="users")
     * @var Directory
     **/
    protected $directory;

    /**
     * @var Collection<UserGroup>
     * @ORM\ManyToMany(inversedBy="users")
     */
    protected $groups;

    /**
     * @ORM\OneToMany(mappedBy="user")
     * @var Collection<IntermediateKey>
     */
    protected $intermediateKey;

    /**
     * @ORM\OneToOne(mappedBy="user")
     * @var WorkQueue
     */
    protected $workQueue;

    /**
     * entity class constructor
     */
    public function __construct()
    {
        $this->intermediateKey = new ArrayCollection();
        $this->groups = new ArrayCollection();
    }

     /**
     * returns id of this entity
     *
     * @return int
     */
    public function getUserId(): int
    {
        return $this->id;
    }

    /**
     * returns passwordhash
     *
     * @return string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * set a new Password, generate every time a new salt
     *
     * @param string $password the password
     *
     * @return $this
     * @throws \Exception
     */
    public function setPassword(string $password): User
    {
        if (CRYPT_BLOWFISH !== 1) {
            throw new \Exception("CRITICAL: BLOWFISH encryption not available");
        }

        $salt = $this->generateBlowFishSalt();

        $hashedPassword = crypt($password, $salt);

        $this->password = $hashedPassword;

        return $this;
    }

    /**
     * generates a valid blowFish salt
     *
     * @return string
     */
    protected function generateBlowFishSalt(): string
    {
        $blowFishPrefix = '$2y$14$';

        $salt = $this->generateSalt(22);

        $blowFishSalt = $blowFishPrefix . $salt;

        return $blowFishSalt;
    }


    /**
     * checks if given password is correct
     *
     * @param Person $person the person value object
     *
     * @return bool
     */
    public function checkPassword(Person $person): bool
    {
        $hashedPassword = $this->getPassword();

        if (crypt($person->getPassword(), $hashedPassword) === $hashedPassword) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns the value of the class member email.
     *
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * sets Email-Address
     *
     * @param string $email the email-address
     *
     * @return $this
     */
    public function setEmail(string $email): User
    {
        $this->email = $email;
        return $this;
    }


    /**
     * Returns users full name
     *
     * @return string
     */
    public function getFullName(): string
    {
        return $this->fullName;
    }

    /**
     * set full name
     *
     * @param string $fullName the full name
     *
     * @return $this
     */
    public function setFullName(string $fullName): User
    {
        $this->fullName = $fullName;
        return $this;
    }


    /**
     * returns username
     *
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * set username
     *
     * @param string $username the username
     *
     * @return $this
     */
    public function setUsername(string $username): User
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Returns the private RSA Key
     *
     * @return string
     */
    public function getPrivateKey(): ?string
    {
        return $this->privateKey;
    }

    /**
     * Sets RSA PrivateKey
     *
     * @param string $privateKey the aes encrypted privateKey
     *
     * @return $this
     */
    public function setPrivateKey(string $privateKey): User
    {
        $this->privateKey = $privateKey;
        return $this;
    }

    /**
     * Returns public RSA key
     *
     * @return string|null
     */
    public function getPublicKey(): ?string
    {
        return $this->publicKey;
    }

    /**
     * Sets RSA PublicKey
     *
     * @param string $publicKey the public key
     *
     * @return $this
     */
    public function setPublicKey(string $publicKey): User
    {
        $this->publicKey = $publicKey;
        return $this;
    }

    /**
     * Returns user's RSA PublicKey passphrase Salt
     *
     * @return string $rsaSalt
     */
    public function getRsaSalt(): string
    {
        return $this->rsaSalt;
    }

    /**
     * Sets user's RSA private key passphrase Salt
     *
     * @param string $rsaSalt the rsa salt
     *
     * @return $this
     */
    public function setRsaSalt(string $rsaSalt): User
    {
        $this->rsaSalt = $rsaSalt;
        return $this;
    }

    /**
     * Alias for getEnabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * check if user is deleted on Directory
     *
     * @return bool
     */
    public function isDeleted(): bool
    {
        if (is_null($this->deleted)) {
            return false;
        }
        return $this->deleted;
    }

    /**
     * Sets enable Attribute to true
     *
     * @return $this
     */
    public function enable(): User
    {
        $this->enabled = true;
        return $this;
    }

    /**
     * Sets enable Attribute to false
     *
     * @return $this
     */
    public function disable(): User
    {
        $this->enabled = false;
        return $this;
    }

    /**
     * flag or unflag user as deleted
     *
     * @param bool $flag boolean flag if user is deleted or not
     *
     * @return $this
     */
    public function setDeletedFlag(bool $flag = true): User
    {
        $this->deleted = $flag;
        return $this;
    }

    /**
     * sets directory where user is coming from
     *
     * @param Directory $directory the directory entity
     *
     * @return $this
     */
    public function setDirectory(Directory $directory): User
    {
        $this->directory = $directory;
        return $this;
    }

    /**
     * return the source directory
     *
     * @return Directory
     */
    public function getDirectory(): Directory
    {
        return $this->directory;
    }

    /**
     * add this user to a new group
     *
     * @param UserGroup $group a user group entity instance
     *
     * @return bool
     */
    public function addToGroup(UserGroup $group): bool
    {
        if (!$this->getGroups()->contains($group)) {
            $this->getGroups()->add($group);
            $group->addUser($this);
            return true;
        }

        return false;
    }

    /**
     * remove user from a group
     *
     * @param UserGroup $group a user group entity instance
     *
     * @return $this
     */
    public function removeFromGroup(UserGroup $group): User
    {
        if ($this->getGroups()->contains($group)) {
            $this->getGroups()->removeElement($group);
            $group->removeUser($this);
        }

        return $this;
    }

    /**
     * get collection containing all groups where this user is a member
     *
     * @return Collection
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    /**
     * @param int $length
     *
     * @return string
     */
    protected function generateSalt(int $length = self::SALTLENGTH): string
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
     * indicate if user has complete the setup process
     *
     * @return bool
     */
    public function isSetupComplete(): bool
    {
        if ($this->getPrivateKey()) {
            return true;
        }
        return false;
    }

    /**
     * Returns user specific (expanded state) group tree settings stored as serialized array
     *
     * @return array
     * @throws \Exception
     */
    public function getTreeSettings(): array
    {
        if ($this->treeSettings === null) {
            return [];
        }
        $result = json_decode($this->treeSettings, true);

        if (!is_array($result)) {
            $result = @unserialize($this->treeSettings);
        }

        if (!is_array($result)) {
            throw new \Exception("error while decoding users tree settings for user with id '{$this->id}'");
        }

        return $result;
    }

    /**
     * Saves the user specific group tree settings
     *
     * @param array $settings user specific group tree settings
     *
     * @return void
     */
    public function setTreeSettings(array $settings): void
    {
        $this->treeSettings = json_encode($settings);
    }

    /**
     * Retrun a array containing all intermediate keys for user
     *
     * @return ArrayCollection
     */
    public function getIntermediateKeys(): object
    {
        return $this->intermediateKey;
    }

    /**
     * Sets the preferred language for this user
     *
     * @param string $lang the language
     *
     * @return void
     */
    public function setLanguage(string $lang): void
    {
        $this->language = $lang;
    }

    /**
     * Returns the language code
     *
     * @return string|null
     */
    public function getLanguage(): ?string
    {
        return $this->language;
    }

    /**
     * Returns user data as array
     *
     * @return array
     */
    public function asArray(): array
    {
        $ar = array();
        $ar['userId'] = $this->getUserId();
        $ar['userName'] = $this->getUsername();
        $ar['fullName'] = $this->getFullName();
        $ar['emailAddress'] = $this->getEmail();
        $ar['enabled'] = $this->isEnabled();
        $ar['deleted'] = $this->isDeleted();
        $ar['setupCompleted'] = $this->isSetupComplete();

        $ar['directoryType'] = $this->getDirectory()->getType();

        if ($ar['directoryType'] === "internal") {
            $ar['directoryName'] = "internal";
        } else {
            $ar['directoryName'] = $this->getDirectory()->getDirectoryName();
        }
        foreach ($this->getGroups() as $group) {
            if (isset($ar['groups'])) {
                $ar['groups'] = $ar['groups'] . ", " . $group->getName();
            } else {
                $ar['groups'] = $group->getName();
            }
        }

        return $ar;
    }

    /**
     * Sets the preferred theme for this user
     *
     * @param string $theme the theme identifier
     *
     * @return void
     */
    public function setTheme(string $theme): void
    {
        $this->theme = $theme;
    }

    /**
     * Sets the preferred theme for this user
     *
     * @return string
     */
    public function getTheme(): ?string
    {
        return $this->theme;
    }

    /**
     * @return bool
     */
    public function isAlphabeticalOrder(): bool
    {
        if (is_null($this->alphabeticalOrder)) {
            return false;
        }
        return $this->alphabeticalOrder;
    }

    /**
     * @param bool $alphabeticalOrder
     */
    public function setAlphabeticalOrder(bool $alphabeticalOrder): void
    {
        $this->alphabeticalOrder = $alphabeticalOrder;
    }
}
