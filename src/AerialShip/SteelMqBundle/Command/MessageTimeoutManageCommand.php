<?php

namespace AerialShip\SteelMqBundle\Command;

use AerialShip\SteelMqBundle\Model\Repository\MessageRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MessageTimeoutManageCommand extends Command
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
        $this->setName('steel:message:timeout:manage')
            ->addOption('force', null, InputOption::VALUE_NONE)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (false == $input->getOption('force')) {
            throw new \RuntimeException('Use --force to really manage timed-out messages');
        }

        list($totalCount, $nonDeletedCount) = $this->messageRepository->manageTimeout();

        $output->writeln(sprintf('Total %s messages timed-out, of which %s kept', $totalCount, $nonDeletedCount));
    }
}
