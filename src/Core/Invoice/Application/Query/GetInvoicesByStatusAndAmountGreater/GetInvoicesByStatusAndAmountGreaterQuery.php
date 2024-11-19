<?php

declare(strict_types=1);

namespace App\Core\Invoice\Application\Query\GetInvoicesByStatusAndAmountGreater;

use App\Core\Invoice\Domain\Status\InvoiceStatus;

final class GetInvoicesByStatusAndAmountGreaterQuery
{
    public function __construct(public readonly InvoiceStatus $status, public readonly int $amount)
    {
    }
}
