<?php

declare(strict_types=1);

namespace App\Core\User\Application\Query\GetEmailsForInactiveUsers;

use App\Core\User\Application\Query\GetEmailsForInactiveUsers\DTO\UserEmail;
use App\Core\User\Domain\Repository\UserReadRepositoryInterface;
use App\Core\User\Domain\Status\UserStatus;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GetEmailsForInactiveUsersHandler
{
    public function __construct(private readonly UserReadRepositoryInterface $userRepository)
    {
    }

    /**
     * @return array<UserEmail>
     */
    public function __invoke(GetEmailsForInactiveUsersQuery $query): array
    {
        $users = $this->userRepository->findByStatus(UserStatus::INACTIVE);

        return array_map(static fn (array $user) => new UserEmail($user['email']), $users);
    }
}
