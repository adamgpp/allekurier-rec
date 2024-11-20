<?php

declare(strict_types=1);

namespace App\Core\User\Infrastructure\Repository;

use App\Core\Common\Domain\ValueObject\Email;
use App\Core\User\Domain\Repository\UserReadRepositoryInterface;
use App\Core\User\Domain\Status\UserStatus;
use App\Core\User\Domain\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Ulid;

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

    public function findByEmail(Email $email): array
    {
        $result = $this->entityManager
            ->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.email = :user_email')
            ->setParameter('user_email', $email->value)
            ->getQuery()
            ->getArrayResult();

        return $result[0] ?? [];
    }

    public function existsById(Ulid $id): bool
    {
        return false === empty($this->entityManager
                ->createQueryBuilder()
                ->select('u')
                ->from(User::class, 'u')
                ->where('u.id = :user_id')
                ->setParameter('user_id', $id->toBinary())
                ->getQuery()
                ->getArrayResult());
    }
}
