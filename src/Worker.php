<?php

declare(strict_types=1);

namespace Solo\AsyncEventDispatcher;

use Solo\AsyncEventDispatcher\Adapter\AsyncAdapterInterface;

final class Worker
{
    public function __construct(
        private readonly AsyncAdapterInterface $adapter,
        private readonly SerializerInterface $serializer = new NativeSerializer()
    ) {
    }

    /**
     * @param null|callable(string $class, string $method): callable $resolver
     */
    public function run(?callable $resolver = null): void
    {
        $resolver ??= static function (string $class, string $method): callable {
            $instance = new $class();
            return [$instance, $method];
        };

        $this->adapter->consume(function (EventMessage $message) use ($resolver): void {
            $event = $this->serializer->unserialize($message->eventPayload, $message->eventClass);
            $callable = $resolver($message->listenerClass, $message->listenerMethod);
            $callable($event);
        });
    }
}


