<?php

/**
 * Listener Reference
 *
 * PHP version 8
 */

declare(strict_types=1);

namespace Solo\AsyncEventDispatcher;

/**
 * Value object representing a listener reference
 */
final readonly class ListenerReference
{
    /**
     * Constructor
     *
     * @param string      $class  Listener class name
     * @param string|null $method Listener method name
     */
    public function __construct(
        public string $class,
        public ?string $method = null
    ) {
    }

    /**
     * Get listener method name
     *
     * @return string
     */
    public function method(): string
    {
        return $this->method ?? '__invoke';
    }
}
