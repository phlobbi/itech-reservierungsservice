<?php

namespace App\Command;

use App\Service\AvailableTimesService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'itech:create-times', description: 'Create times for the next 7 days'
)]
class CreateTimesCommand extends Command
{

    public function __construct(
        private readonly AvailableTimesService $availableTimesService
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->availableTimesService->createTimes();

        $output->writeln('Times created');

        return Command::SUCCESS;
    }
}