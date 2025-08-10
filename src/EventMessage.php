<?php

declare(strict_types=1);

namespace Solo\AsyncEventDispatcher;

final readonly class EventMessage
{
    public function __construct(
        public string $eventClass,
        public string $eventPayload,
        public string $listenerClass,
        public string $listenerMethod,
    ) {
    }
}


