<?php

declare(strict_types=1);

namespace App\Core\User\UserInterface\Cli;

use App\Common\Bus\QueryBusInterface;
use App\Core\User\Application\Query\GetEmailsForInactiveUsers\DTO\UserEmail;
use App\Core\User\Application\Query\GetEmailsForInactiveUsers\GetEmailsForInactiveUsersQuery;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:user:get-email-for-inactive-users',
    description: 'Pobieranie adresów e-mail nieaktywnych użytkowników'
)]
final class GetEmailsForInactiveUsers extends Command
{
    public function __construct(private readonly QueryBusInterface $bus)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var array<UserEmail> $userEmails */
        $userEmails = $this->bus->dispatch(new GetEmailsForInactiveUsersQuery());

        $this->displayResult($output, ...$userEmails);

        return Command::SUCCESS;
    }

    private function displayResult(OutputInterface $output, UserEmail ...$userEmails): void
    {
        foreach ($userEmails as $userEmail) {
            $output->writeln($userEmail->email);
        }
    }
}
