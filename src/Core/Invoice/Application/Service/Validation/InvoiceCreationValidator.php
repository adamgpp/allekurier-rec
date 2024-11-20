<?php

declare(strict_types=1);

namespace App\Core\Invoice\Application\Service\Validation;

use App\Core\Common\Domain\ValueObject\Email;
use App\Core\Invoice\Application\Service\Exception\InvoiceCreationException;
use App\Core\Invoice\Domain\Repository\InvoiceReadRepositoryInterface;
use App\Core\User\Domain\Repository\UserReadRepositoryInterface;
use App\Core\User\Domain\Status\UserStatus;
use Symfony\Component\Uid\Ulid;

final class InvoiceCreationValidator implements InvoiceCreationValidationInterface
{
    public function __construct(
        private readonly InvoiceReadRepositoryInterface $invoiceReadRepository,
        private readonly UserReadRepositoryInterface $userReadRepository,
    )
    {
    }

    public function assertInvoiceCanBeCreated(Ulid $invoiceId, Email $userEmail): void
    {
        if ($this->invoiceReadRepository->existsById($invoiceId)) {
            throw InvoiceCreationException::invoiceWithIdAlreadyExists($invoiceId);
        }

        $user = $this->userReadRepository->findByEmail($userEmail);

        if (empty($user)) {
            throw InvoiceCreationException::userWithEmailNotExists($userEmail);
        }

        if (UserStatus::ACTIVE !== $user['status']) {
            throw InvoiceCreationException::userIsNotActive($user['id']);
        }
    }
}
