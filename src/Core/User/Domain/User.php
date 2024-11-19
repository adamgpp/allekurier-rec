<?php

namespace App\Core\User\Domain;

use App\Common\EventManager\EventsCollectorTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User
{
    use EventsCollectorTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'ulid', unique: true)]
    private Ulid $id;

    #[ORM\Column(type: 'string', length: 300, nullable: false)]
    private string $email;

    #[ORM\Column(type: 'string', nullable: false, enumType: UserStatus::class)]
    private UserStatus $status;

    public function __construct(Ulid $id, string $email)
    {
        $this->id = $id;
        $this->email = $email;
        $this->status = UserStatus::INACTIVE;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
