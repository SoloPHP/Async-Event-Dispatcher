<?php

/**
 * Event Message
 *
 * PHP version 8
 */

declare(strict_types=1);

namespace Solo\AsyncEventDispatcher;

/**
 * Value object representing an event message for async processing
 */
final readonly class EventMessage
{
    /**
     * Constructor
     *
     * @param string $eventClass     Event class name
     * @param string $eventPayload   Serialized event data
     * @param string $listenerClass  Listener class name
     * @param string $listenerMethod Listener method name
     */
    public function __construct(
        public string $eventClass,
        public string $eventPayload,
        public string $listenerClass,
        public string $listenerMethod,
    ) {
    }
}
