<?php

declare(strict_types=1);

namespace App\Core\User\Domain\Repository;

use App\Core\Common\Domain\ValueObject\Email;
use App\Core\User\Domain\Status\UserStatus;
use Symfony\Component\Uid\Ulid;

interface UserReadRepositoryInterface
{
    public function findByStatus(UserStatus $status): array;

    public function existsByEmail(Email $userEmail): bool;

    public function existsById(Ulid $id): bool;
}
