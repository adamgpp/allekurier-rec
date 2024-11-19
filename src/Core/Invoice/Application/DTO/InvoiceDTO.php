<?php

declare(strict_types=1);

namespace App\Core\Invoice\Application\DTO;

use App\Core\Common\Domain\ValueObject\Email;
use App\Core\Invoice\Domain\ValueObject\Amount;
use Symfony\Component\Uid\Ulid;

final class InvoiceDTO
{
    public function __construct(
        public readonly Ulid $id,
        public readonly Email $email,
        public readonly Amount $amount
    ) {}
}
