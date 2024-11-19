<?php

declare(strict_types=1);

namespace App\Common\Bus;

use Symfony\Component\Messenger\MessageBusInterface;

final class CommandBus implements CommandBusInterface
{
    public function __construct(private readonly MessageBusInterface $bus)
    {
    }

    public function dispatch(CommandInterface $command): void
    {
        try {
            $this->bus->dispatch($command);
        } catch (\Throwable $e) {
            while (true) {
                if ($e instanceof \DomainException) {
                    throw $e;
                }

                if (null === $e->getPrevious()) {
                    throw $e;
                }

                $e = $e->getPrevious();
            }
        }
    }
}
