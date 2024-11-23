<?php

declare(strict_types=1);

namespace App\Core\User\Domain\Feature\UserCreation\Validation;

use App\Core\Common\Domain\ValueObject\Email;
use Symfony\Component\Uid\Ulid;

interface UserCreationValidationInterface
{
    public function assertUserCanBeCreated(Ulid $userId, Email $userEmail): void;
}
