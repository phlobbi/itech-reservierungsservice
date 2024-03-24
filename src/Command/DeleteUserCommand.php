<?php

namespace App\Command;

use App\Service\UserService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'itech:delete-user',
    description: 'Deletes a user.',
    hidden: false
)]
class DeleteUserCommand extends Command
{

    public function __construct(private readonly UserService $userService) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $username = $input->getArgument('username');

        $output->writeln([
            'Deleting User with the following details:'
        ]);

        $output->writeln('Username: ' . $username);

        try {
            $this->userService->deleteUser($username);
        } catch (\Exception $e) {
            $output->writeln('Error while deleting User: ' . $e->getMessage());
            return Command::FAILURE;
        }

        $output->writeln('User successfully deleted.');

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Deletes a user.')
            ->setHelp('This command allows you to delete a user.')
            ->addArgument('username', InputArgument::REQUIRED, 'The username of the user.');
    }
}