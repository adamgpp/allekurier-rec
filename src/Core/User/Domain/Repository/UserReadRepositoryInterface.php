<?php

declare(strict_types=1);

namespace App\Core\User\Domain\Repository;

use App\Core\User\Domain\Status\UserStatus;

interface UserReadRepositoryInterface
{
    public function findByStatus(UserStatus $status): array;
}
