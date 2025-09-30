<?php

/**
 * Native Serializer
 *
 * PHP version 8
 */

declare(strict_types=1);

namespace Solo\AsyncEventDispatcher;

/**
 * Native PHP serializer implementation
 */
final class NativeSerializer implements SerializerInterface
{
    /**
     * Serialize an event object to string
     *
     * @param object $event Event to serialize
     *
     * @return string
     */
    public function serialize(object $event): string
    {
        return serialize($event);
    }

    /**
     * Unserialize string payload to event object
     *
     * @param string $payload    Serialized event data
     * @param string $eventClass Expected event class name
     *
     * @return object
     */
    public function unserialize(string $payload, string $eventClass): object
    {
        /** @var object $event */
        $event = unserialize($payload, ['allowed_classes' => true]);

        if (!$event instanceof $eventClass) {
            throw new \RuntimeException(
                "Unserialized event is not instance of {$eventClass}"
            );
        }

        return $event;
    }
}
