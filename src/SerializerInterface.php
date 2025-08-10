<?php

declare(strict_types=1);

namespace Solo\AsyncEventDispatcher;

interface SerializerInterface
{
    public function serialize(object $event): string;

    public function unserialize(string $payload, string $eventClass): object;
}


