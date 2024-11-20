<?php

declare(strict_types=1);

namespace App\Core\User\Domain;

use App\Core\Common\Domain\ValueObject\Email;
use App\Core\User\Domain\Status\UserStatus;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User
{
    #[ORM\Id]
    #[ORM\Column(type: 'ulid', unique: true)]
    private Ulid $id;

    #[ORM\Column(type: 'string', length: 300, unique: true, nullable: false)]
    private string $email;

    #[ORM\Column(type: 'string', nullable: false, enumType: UserStatus::class)]
    private UserStatus $status;

    public function __construct(Ulid $id, Email $email)
    {
        $this->id = $id;
        $this->email = $email->value;
        $this->status = UserStatus::INACTIVE;
    }

    public function getId(): Ulid
    {
        return $this->id;
    }

    public function getEmail(): Email
    {
        return new Email($this->email);
    }

    public function isActive(): bool
    {
        return UserStatus::ACTIVE === $this->status;
    }

    /**
     * Only for testing purposes. In a real world this should be triggered inside domain feature, ex. via CLI command.
     */
    public function activate(): void
    {
        $this->status = UserStatus::ACTIVE;
    }
}
