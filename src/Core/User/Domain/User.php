<?php

namespace App\Core\User\Domain;

use App\Common\EventManager\EventsCollectorTrait;
use App\Core\Common\Domain\ValueObject\Email;
use App\Core\User\Domain\Event\UserCreatedEvent;
use App\Core\User\Domain\Status\UserStatus;
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

    #[ORM\Column(type: 'string', length: 300, unique: true, nullable: false)]
    private string $email;

    #[ORM\Column(type: 'string', nullable: false, enumType: UserStatus::class)]
    private UserStatus $status;

    public function __construct(Ulid $id, Email $email)
    {
        $this->id = $id;
        $this->email = $email->value;
        $this->status = UserStatus::INACTIVE;

        $this->record(new UserCreatedEvent($this));
    }

    public function getEmail(): Email
    {
        return new Email($this->email);
    }
}
