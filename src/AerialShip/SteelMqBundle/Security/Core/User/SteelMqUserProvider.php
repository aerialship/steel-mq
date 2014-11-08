<?php

namespace AerialShip\SteelMqBundle\Security\Core\User;

use AerialShip\SteelMqBundle\Entity\User;
use AerialShip\SteelMqBundle\Model\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class SteelMqUserProvider implements UserProviderInterface
{
    /** @var  UserRepositoryInterface */
    protected $userRepository;

    /**
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username The username
     *
     * @return UserInterface
     *
     * @see UsernameNotFoundException
     *
     * @throws UsernameNotFoundException if the user is not found
     *
     */
    public function loadUserByUsername($username)
    {
        $result = $this->userRepository->getByUsername($username);

        if (false == $result) {
            throw new UsernameNotFoundException();
        }

        return $result;
    }

    /**
     * Refreshes the user for the account interface.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     * @param UserInterface $user
     *
     * @return UserInterface
     *
     * @throws UnsupportedUserException if the account is not supported
     */
    public function refreshUser(UserInterface $user)
    {
        if ($user instanceof User) {
            $result = $this->userRepository->getById($user->getId());
            if ($result) {
                return $result;
            }
        }

        throw new UnsupportedUserException();
    }

    /**
     * Whether this provider supports the given user class
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return 'AerialShip\SteelMqBundle\Entity\User' == $class ||
            is_subclass_of($class, 'AerialShip\SteelMqBundle\Entity\User');
    }

}
