<?php

namespace App\Core\Invoice\UserInterface\Cli;

use App\Core\Common\Domain\ValueObject\Email;
use App\Core\Common\Domain\ValueObject\Exception\ValueObjectValidationException;
use App\Core\Invoice\Application\Command\CreateInvoice\CreateInvoiceCommand;
use App\Core\Invoice\Domain\ValueObject\Amount;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Ulid;

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
            $amount = new Amount((int) $input->getArgument('amount'));
        } catch (ValueObjectValidationException $e) {
            $output->writeln($e->getMessage());

            return Command::INVALID;
        }

        $id = new Ulid();

        $this->bus->dispatch(new CreateInvoiceCommand($id, $email, $amount));

        $output->writeln(sprintf('A new invoice has been created. ID: %s.', $id->toRfc4122()));

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addArgument('email', InputArgument::REQUIRED);
        $this->addArgument('amount', InputArgument::REQUIRED);
    }
}
