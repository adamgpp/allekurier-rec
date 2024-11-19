<?php

declare(strict_types=1);

namespace App\Core\Invoice\Application\Command\CreateInvoice;

use App\Core\Invoice\Domain\Invoice;
use App\Core\Invoice\Domain\Repository\InvoiceWriteRepositoryInterface;
use App\Core\User\Domain\Repository\UserWriteRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CreateInvoiceHandler
{
    public function __construct(
        private readonly InvoiceWriteRepositoryInterface $invoiceWriteRepository,
        private readonly UserWriteRepositoryInterface $userRepository
    ) {}

    public function __invoke(CreateInvoiceCommand $command): void
    {
        $this->invoiceWriteRepository->save(new Invoice(
            $command->id,
            $this->userRepository->getByEmail($command->email),
            $command->amount
        ));

        $this->invoiceWriteRepository->flush();
    }
}
