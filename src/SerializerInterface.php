<?php

/**
 * Serializer Interface
 *
 * PHP version 8
 */

declare(strict_types=1);

namespace Solo\AsyncEventDispatcher;

/**
 * Interface for event serialization
 */
interface SerializerInterface
{
    /**
     * Serialize an event object to string
     *
     * @param object $event Event to serialize
     *
     * @return string
     */
    public function serialize(object $event): string;

    /**
     * Unserialize string payload to event object
     *
     * @param string $payload    Serialized event data
     * @param string $eventClass Expected event class name
     *
     * @return object
     */
    public function unserialize(string $payload, string $eventClass): object;
}
