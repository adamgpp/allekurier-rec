<?php

declare(strict_types=1);

namespace App\Core\User\Domain\Repository;

use App\Core\Common\Domain\ValueObject\Email;
use App\Core\User\Domain\User;

interface UserWriteRepositoryInterface
{
    public function save(User $user): void;

    public function flush(): void;

    public function getByEmail(Email $email): User;
}
