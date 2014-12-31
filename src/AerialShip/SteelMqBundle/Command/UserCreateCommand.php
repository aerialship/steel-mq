<?php

namespace AerialShip\SteelMqBundle\Command;

use AerialShip\SteelMqBundle\Entity\User;
use AerialShip\SteelMqBundle\Model\Repository\UserRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UserCreateCommand extends Command
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
        $this->setName('steel:user:create')
            ->addArgument('email', InputArgument::REQUIRED)
            ->addOption('name', null, InputOption::VALUE_REQUIRED)
            ->addOption('password', null, InputOption::VALUE_REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument('email');
        $this->checkIfAlreadyExists($email);

        $user = new User();
        $user->setEmail($email)
            ->setName($input->getOption('name') ?: $email)
            ->setPlainPassword($password = ($input->getOption('password') ?: $this->generatePassword(10)))
            ->setCreatedAt(new \DateTime())
            ->setAccessToken($this->generatePassword(18))
        ;
        $this->userRepository->save($user);

        if (false == $input->getOption('password')) {
            $output->writeln($password);
        }
    }

    /**
     * @param string $email
     */
    protected function checkIfAlreadyExists($email)
    {
        $user = $this->userRepository->getByUsername($email);
        if ($user) {
            throw new \RuntimeException(sprintf("User %s already exists", $email));
        }
    }

    /**
     * @return string
     */
    protected function generatePassword($length)
    {
        return substr(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36), $length);
    }
}
