<?php

declare(strict_types=1);

namespace App\Core\Invoice\Application\Query\GetInvoicesByStatusAndAmountGreater\DTO;

use Symfony\Component\Uid\Ulid;

final class InvoiceId
{
    public function __construct(
        public readonly Ulid $id,
    )
    {
    }
}
