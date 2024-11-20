<?php

namespace App\Common\Bus;

interface QueryBusInterface
{
    /**
     * Dispatches the given message.
     *
     * @param object $message
     */
    public function dispatch($message, array $stamps = []): mixed;
}
