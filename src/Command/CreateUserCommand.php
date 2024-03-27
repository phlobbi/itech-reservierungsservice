<?php

namespace App\Command;

use App\Service\UserService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'itech:create-user',
    description: 'Creates a new user',
    hidden: false
)]
class CreateUserCommand extends Command
{

    public function __construct(private UserService $userService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');

        $output->writeln([
            'Creating User with the following details:'
        ]);

        $output->writeln('Username: ' . $username);
        $output->writeln('Password: ' . $password);

        try {
            $this->userService->registerUser($username, $password);
        } catch (\Exception $e) {
            $output->writeln('Error while registering User: ' . $e->getMessage());
            return Command::FAILURE;
        }

        $output->writeln('User registered successfully.');

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Creates a new user.')
            ->setHelp('This command allows you to create a new user.')
            ->addArgument('username', InputArgument::REQUIRED, 'The username of the user.')
            ->addArgument('password', InputArgument::REQUIRED, 'The password of the user.');
    }
}