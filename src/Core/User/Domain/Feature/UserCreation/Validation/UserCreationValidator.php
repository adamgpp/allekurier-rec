<?php

declare(strict_types=1);

namespace App\Core\User\Domain\Feature\UserCreation\Validation;

use App\Core\Common\Domain\ValueObject\Email;
use App\Core\User\Domain\Feature\UserCreation\Exception\UserCreationException;
use App\Core\User\Domain\Repository\UserReadRepositoryInterface;
use Symfony\Component\Uid\Ulid;

final class UserCreationValidator implements UserCreationValidationInterface
{
    public function __construct(
        private readonly UserReadRepositoryInterface $userReadRepository,
    ) {
    }

    public function assertUserCanBeCreated(Ulid $userId, Email $userEmail): void
    {
        if ($this->userReadRepository->existsById($userId)) {
            throw UserCreationException::userWithIdAlreadyExists($userId);
        }

        if (false === empty($this->userReadRepository->findByEmail($userEmail))) {
            throw UserCreationException::userWithEmailAlreadyExists($userEmail);
        }
    }
}
