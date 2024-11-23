<?php

declare(strict_types=1);

namespace App\Core\Invoice\Application\Command\CreateInvoice;

use App\Core\Invoice\Domain\Feature\InvoiceCreation\InvoiceCreationInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CreateInvoiceHandler
{
    public function __construct(
        private readonly InvoiceCreationInterface $invoiceCreator,
    ) {
    }

    public function __invoke(CreateInvoiceCommand $command): void
    {
        $this->invoiceCreator->createInvoice($command->id, $command->userEmail, $command->amount);
    }
}
