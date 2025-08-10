<?php

declare(strict_types=1);

namespace Solo\AsyncEventDispatcher;

final readonly class ListenerReference
{
    public function __construct(
        public string $class,
        public ?string $method = null
    ) {
    }

    public function method(): string
    {
        return $this->method ?? '__invoke';
    }
}


