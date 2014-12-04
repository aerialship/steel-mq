<?php

namespace AerialShip\SteelMqBundle\Command;

use AerialShip\SteelMqBundle\Entity\User;
use AerialShip\SteelMqBundle\Model\Repository\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Command\AbstractConfigCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserPromoteCommand extends AbstractConfigCommand
{
    /** @var  UserRepositoryInterface */
    protected $userRepository;

    /**
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        parent::__construct();

        $this->userRepository = $userRepository;
    }

    protected function configure()
    {
        $this->setName('steel:user:promote')
            ->addArgument('email', InputArgument::REQUIRED)
            ->addArgument('role', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = $this->getUser($input->getArgument('email'));

        $user->addRole($this->getRole($input));
        $this->userRepository->save($user);
    }

    /**
     * @param string $email
     *
     * @return \AerialShip\SteelMqBundle\Entity\User
     */
    protected function getUser($email)
    {
        $user = $this->userRepository->getByUsername($email);
        if (false == $user) {
            throw new \RuntimeException(sprintf("User %s does not exist", $email));
        }

        return $user;
    }

    /**
     * @param InputInterface $input
     *
     * @return string
     */
    protected function getRole(InputInterface $input)
    {
        $role = $input->getArgument('role');
        if (false === User::isValidRole($role)) {
            throw new \RuntimeException(sprintf("Invalid role '%s'", $role));
        }

        return $role;
    }
}
