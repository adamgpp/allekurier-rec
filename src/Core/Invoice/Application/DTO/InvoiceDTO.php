<?php

namespace App\Core\Invoice\Application\DTO;

use Symfony\Component\Uid\Ulid;

class InvoiceDTO
{
    public function __construct(
        public readonly Ulid $id,
        public readonly string $email,
        public readonly int $amount
    ) {}
}
