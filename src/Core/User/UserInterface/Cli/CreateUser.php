<?php

declare(strict_types=1);

namespace App\Core\User\UserInterface\Cli;

use App\Core\Common\Domain\ValueObject\Email;
use App\Core\Common\Domain\ValueObject\Exception\ValueObjectValidationException;
use App\Core\User\Application\Command\CreateUser\CreateUserCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:user:create',
    description: 'Dodawanie nowego użytkownika'
)]
final class CreateUser extends Command
{
    public function __construct(private readonly MessageBusInterface $bus)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $email = new Email($input->getArgument('email'));
        } catch (ValueObjectValidationException $e) {
            $output->writeln($e->getMessage());

            return Command::INVALID;
        }

        $this->bus->dispatch(new CreateUserCommand($email));

        $output->writeln('A new user has been successfully created.');

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addArgument('email', InputArgument::REQUIRED);
    }
}
