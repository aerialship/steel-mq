<?php

namespace AerialShip\SteelMqBundle\Command;

use AerialShip\SteelMqBundle\Entity\User;
use AerialShip\SteelMqBundle\Model\Repository\UserRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserPasswdCommand extends Command
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
        $this->setName('steel:user:passwd')
            ->addArgument('email', InputArgument::REQUIRED)
            ->addArgument('password', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = $this->getUser($input->getArgument('email'));

        $user
            ->setPlainPassword($input->getArgument('password'))
            ->setPassword(mt_rand(10000, 999999))
        ;
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
        if (false === $user) {
            throw new \RuntimeException(sprintf("User %s does not exist", $email));
        }

        return $user;
    }
}
