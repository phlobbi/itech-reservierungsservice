<?php

namespace App\Command;

use App\Service\AvailableTimesService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'itech:delete-old-times',
    description: 'Deletes old times from the database'
)]
class DeleteOldTimesCommand extends Command
{

    public function __construct(private AvailableTimesService $availableTimesService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Deleting old times');

        $this->availableTimesService->deleteOldTimes();

        $output->writeln('Old times deleted');

        return Command::SUCCESS;
    }
}