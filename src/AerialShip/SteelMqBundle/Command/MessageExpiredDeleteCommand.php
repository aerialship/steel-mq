<?php

namespace AerialShip\SteelMqBundle\Command;

use AerialShip\SteelMqBundle\Model\Repository\MessageRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MessageExpiredDeleteCommand extends Command
{
    /** @var MessageRepositoryInterface */
    private $messageRepository;

    /**
     * @param MessageRepositoryInterface $messageRepository
     */
    public function __construct(MessageRepositoryInterface $messageRepository)
    {
        parent::__construct();

        $this->messageRepository = $messageRepository;
    }

    protected function configure()
    {
        $this->setName('steel:message:expired:delete')
            ->addOption('force', null, InputOption::VALUE_NONE)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (false == $input->getOption('force')) {
            throw new \RuntimeException('Use --force to really delete expired messages');
        }

        $count = $this->messageRepository->deleteExpired();

        $output->writeln(sprintf('Deleted %s messages', $count));
    }
}
