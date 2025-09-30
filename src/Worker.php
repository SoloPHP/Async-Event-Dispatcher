<?php

/**
 * Worker
 *
 * PHP version 8
 */

declare(strict_types=1);

namespace Solo\AsyncEventDispatcher;

use Solo\AsyncEventDispatcher\Adapter\AsyncAdapterInterface;

/**
 * Worker for processing async events
 */
final class Worker
{
    /**
     * Constructor
     *
     * @param AsyncAdapterInterface $adapter    Queue adapter
     * @param SerializerInterface   $serializer Event serializer
     */
    public function __construct(
        private readonly AsyncAdapterInterface $adapter,
        private readonly SerializerInterface $serializer = new NativeSerializer()
    ) {
    }

    /**
     * Run worker to process messages
     *
     * @param null|callable(string $class, string $method): callable $resolver
     *
     * @return void
     */
    public function run(?callable $resolver = null): void
    {
        $resolver ??= static function (
            string $class,
            string $method
        ): callable {
            $instance = new $class();
            /** @var callable */
            return [$instance, $method];
        };

        $this->adapter->consume(
            function (EventMessage $message) use ($resolver): void {
                $event = $this->serializer->unserialize(
                    $message->eventPayload,
                    $message->eventClass
                );
                $callable = $resolver(
                    $message->listenerClass,
                    $message->listenerMethod
                );
                $callable($event);
            }
        );
    }
}
