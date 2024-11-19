<?php

namespace App\Core\User\Domain\Repository;

use App\Core\Common\Domain\ValueObject\Email;
use App\Core\User\Domain\Exception\UserNotFoundException;
use App\Core\User\Domain\User;

interface UserRepositoryInterface
{
    /**
     * @throws UserNotFoundException
     */
    public function getByEmail(Email $email): User;

    public function save(User $user): void;

    public function flush(): void;
}
