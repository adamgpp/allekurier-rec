<?php

declare(strict_types=1);

namespace App\Core\User\Domain\Feature;

use App\Core\Common\Domain\ValueObject\Email;
use Symfony\Component\Uid\Ulid;

interface UserCreationInterface
{
    public function createUser(Ulid $id, Email $email): void;
}
