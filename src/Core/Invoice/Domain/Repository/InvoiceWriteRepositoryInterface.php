<?php

namespace App\Core\Invoice\Domain\Repository;

use App\Core\Invoice\Domain\Invoice;

interface InvoiceWriteRepositoryInterface
{
    public function save(Invoice $invoice): void;

    public function flush(): void;
}
