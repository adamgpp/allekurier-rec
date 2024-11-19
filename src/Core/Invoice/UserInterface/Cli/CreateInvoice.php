<?php

namespace App\Core\Invoice\UserInterface\Cli;

use App\Core\Common\Domain\ValueObject\Email;
use App\Core\Common\Domain\ValueObject\Exception\ValueObjectValidationException;
use App\Core\Invoice\Application\Command\CreateInvoice\CreateInvoiceCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:invoice:create',
    description: 'Dodawanie nowej faktury'
)]
class CreateInvoice extends Command
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

        $this->bus->dispatch(new CreateInvoiceCommand(
            $email,
            $input->getArgument('amount')
        ));

        $output->writeln('A new invoice has been successfully created.');

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addArgument('email', InputArgument::REQUIRED);
        $this->addArgument('amount', InputArgument::REQUIRED);
    }
}
