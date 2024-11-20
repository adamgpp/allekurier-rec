<?php

declare(strict_types=1);

namespace App\Core\Invoice\Domain\Repository;

use App\Core\Invoice\Domain\Status\InvoiceStatus;
use App\Core\Invoice\Domain\ValueObject\Amount;
use Symfony\Component\Uid\Ulid;

interface InvoiceReadRepositoryInterface
{
    /**
     * @return array[]
     */
    public function getInvoicesWithGreaterAmountAndStatus(Amount $amount, InvoiceStatus $invoiceStatus): array;

    public function existsById(Ulid $id): bool;
}
