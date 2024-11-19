<?php

declare(strict_types=1);

namespace App\Core\User\Infrastructure\Persistance;

use App\Core\Common\Domain\ValueObject\Email;
use App\Core\User\Domain\Repository\UserWriteRepositoryInterface;
use App\Core\User\Domain\User;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineUserWriteRepository implements UserWriteRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    )
    {
    }

    public function save(User $user): void
    {
        $this->entityManager->persist($user);
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }

    public function findByEmail(Email $email): ?User
    {
        return $this->entityManager->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.email = :user_email')
            ->setParameter(':user_email', $email->value)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
