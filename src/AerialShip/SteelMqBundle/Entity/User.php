<?php

namespace AerialShip\SteelMqBundle\Entity;

use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="AerialShip\SteelMqBundle\Entity\UserRepository")
 * @ORM\Table(name="smq_user")
 */
class User implements UserInterface, \Serializable
{
    const ROLE_USER = 'ROLE_USER';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
    const ROLE_DEFAULT = self::ROLE_USER;

    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=100, unique=true)
     */
    protected $email;

    /**
     * @var string
     * @ORM\Column(type="string", length=100)
     */
    protected $name;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $lastLogin;

    /**
     * @var string|null
     */
    protected $plainPassword;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    protected $salt;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    protected $password;

    /**
     * @var array
     * @ORM\Column(type="array")
     */
    protected $roles = array();

    /**
     * @var string
     * @ORM\Column(type="string", length=64)
     */
    protected $accessToken;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    protected $passwordToken;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $passwordRequestAt;

    /**
     * @var string
     * @ORM\Column(type="string", length=16)
     */
    protected $locale = 'en';

    /**
     * @var string
     * @ORM\Column(type="string", length=32)
     */
    protected $timezone = 'Europe/Oslo';

    /**
     * @var string
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    protected $pictureUrl;

    /**
     * @var ProjectRole[]|ArrayCollection
     * @ORM\OneToMany(
     *      targetEntity="ProjectRole",
     *      mappedBy="user",
     *      orphanRemoval=true,
     *      cascade={"remove"}
     * )
     */
    protected $projectRoles;

    /**
     *
     */
    public function __construct()
    {
        $this->projectRoles = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     * @return $this|User
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this|User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return $this|User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     * @return $this|User
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * @param \DateTime|null $lastLogin
     * @return $this|User
     */
    public function setLastLogin($lastLogin)
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param null|string $plainPassword
     * @return $this|User
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @param string $timezone
     * @return $this|User
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * @return string
     */
    public function getPictureUrl()
    {
        return $this->pictureUrl;
    }

    /**
     * @param string $pictureUrl
     * @return $this|User
     */
    public function setPictureUrl($pictureUrl)
    {
        $this->pictureUrl = $pictureUrl;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     * @return $this|User
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getPasswordRequestAt()
    {
        return $this->passwordRequestAt;
    }

    /**
     * @param \DateTime|null $passwordRequestAt
     * @return $this|User
     */
    public function setPasswordRequestAt(\DateTime $passwordRequestAt = null)
    {
        $this->passwordRequestAt = $passwordRequestAt;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPasswordToken()
    {
        return $this->passwordToken;
    }

    /**
     * @param null|string $passwordToken
     * @return $this|User
     */
    public function setPasswordToken($passwordToken)
    {
        $this->passwordToken = $passwordToken;

        return $this;
    }

    /**
     * @return ProjectRole[]|ArrayCollection
     */
    public function getProjectRoles()
    {
        return $this->projectRoles;
    }

    /**
     * @param string $role
     * @return User|$this
     */
    public function addRole($role)
    {
        $role = strtoupper($role);
        if ($role === self::ROLE_DEFAULT) {
            return $this;
        }

        if (false == in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * @param string $role
     * @return User|$this
     */
    public function removeRole($role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    /**
     * @param array $roles
     * @return $this|User
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @param string $password
     * @return $this|User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @param string $salt
     * @return $this|User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    // UserInterface ------------------------------------------------------------

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return Role[] The user roles
     */
    public function getRoles()
    {
        $roles = $this->roles;

        // we need to make sure to have at least one role
        $roles[] = self::ROLE_DEFAULT;

        return array_unique($roles);
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    // Serializable ----------------------------------------------------------------------
    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->email,
            $this->name,
            $this->locale,
            $this->timezone,
            $this->pictureUrl,
            $this->roles
        ));
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        // add a few extra elements in the array to ensure that we have enough keys when unserializing
        // older data which does not include all properties.
        $data = array_merge($data, array_fill(0, 2, null));

        list(
            $this->id,
            $this->email,
            $this->name,
            $this->locale,
            $this->timezone,
            $this->pictureUrl,
            $this->roles
        ) = $data;
    }
}
