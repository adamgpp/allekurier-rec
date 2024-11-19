<?php

declare(strict_types=1);

namespace App\Core\User\Infrastructure\Persistance;

use App\Core\User\Domain\Repository\UserReadRepositoryInterface;
use App\Core\User\Domain\Status\UserStatus;
use App\Core\User\Domain\User;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineUserReadRepository implements UserReadRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    )
    {
    }

    public function findByStatus(UserStatus $status): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.status = :user_status')
            ->setParameter('user_status', $status->value)
            ->getQuery()
            ->getArrayResult();
    }
}
