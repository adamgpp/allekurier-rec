<?php

namespace App\Core\Invoice\Application\Query\GetInvoicesByStatusAndAmountGreater;

use App\Core\Invoice\Application\Query\GetInvoicesByStatusAndAmountGreater\DTO\InvoiceId;
use App\Core\Invoice\Domain\Repository\InvoiceReadRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GetInvoicesByStatusAndAmountGreaterHandler
{
    public function __construct(
        private readonly InvoiceReadRepositoryInterface $invoiceRepository,
    ) {
    }

    /**
     * @return array<InvoiceId>
     */
    public function __invoke(GetInvoicesByStatusAndAmountGreaterQuery $query): array
    {
        $invoices = $this->invoiceRepository->getInvoicesWithGreaterAmountAndStatus(
            $query->amount,
            $query->status,
        );

        return array_map(static fn (array $invoice) => new InvoiceId($invoice['id']), $invoices);
    }
}
