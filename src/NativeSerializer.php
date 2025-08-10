<?php

declare(strict_types=1);

namespace Solo\AsyncEventDispatcher;

final class NativeSerializer implements SerializerInterface
{
    public function serialize(object $event): string
    {
        return serialize($event);
    }

    public function unserialize(string $payload, string $eventClass): object
    {
        /** @var object $event */
        $event = unserialize($payload, ['allowed_classes' => true]);

        if (!$event instanceof $eventClass) {
            throw new \RuntimeException("Unserialized event is not instance of {$eventClass}");
        }

        return $event;
    }
}


