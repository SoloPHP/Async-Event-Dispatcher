<?php

/**
 * Async Event Job
 *
 * PHP version 8
 *
 * @category Adapter
 * @package  Solo\AsyncEventDispatcher\Adapter
 * @author   Solo <info@example.com>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/example/async-event-dispatcher
 */

declare(strict_types=1);

namespace Solo\AsyncEventDispatcher\Adapter;

use JsonSerializable;
use Psr\Container\ContainerInterface;
use Solo\AsyncEventDispatcher\NativeSerializer;
use Solo\AsyncEventDispatcher\SerializerInterface;
use Solo\Contracts\JobQueue\JobInterface;

/**
 * Job for processing async events from AsyncEventDispatcher
 *
 * @category Adapter
 * @package  Solo\AsyncEventDispatcher\Adapter
 * @author   Solo <info@example.com>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/example/async-event-dispatcher
 */
final class AsyncEventJob implements JobInterface, JsonSerializable
{
    /**
     * Container instance
     *
     * @var ContainerInterface|null
     */
    private static ?ContainerInterface $container = null;

    /**
     * Constructor
     *
     * @param string              $eventClass     Event class name
     * @param string              $eventPayload   Serialized event data
     * @param string              $listenerClass  Listener class name
     * @param string              $listenerMethod Listener method name
     * @param SerializerInterface $serializer     Event serializer
     */
    public function __construct(
        private readonly string $eventClass,
        private readonly string $eventPayload,
        private readonly string $listenerClass,
        private readonly string $listenerMethod,
        private readonly SerializerInterface $serializer = new NativeSerializer()
    ) {
    }

    /**
     * Set container instance
     *
     * @param ContainerInterface $container DI container
     *
     * @return void
     */
    public static function setContainer(ContainerInterface $container): void
    {
        self::$container = $container;
    }

    /**
     * Serialize job data
     *
     * @return array<string, string>
     */
    public function jsonSerialize(): array
    {
        return [
            'eventClass' => $this->eventClass,
            'eventPayload' => $this->eventPayload,
            'listenerClass' => $this->listenerClass,
            'listenerMethod' => $this->listenerMethod
        ];
    }

    /**
     * Handle the job execution
     *
     * @return void
     */
    public function handle(): void
    {
        if (self::$container === null) {
            throw new \RuntimeException(
                'Container not set. Call AsyncEventJob::setContainer() first.'
            );
        }

        // Deserialize the event
        $event = $this->serializer->unserialize(
            $this->eventPayload,
            $this->eventClass
        );

        // Get the listener from container
        $listener = self::$container->get($this->listenerClass);

        // Call the listener method
        $method = $this->listenerMethod;
        $listener->$method($event);
    }
}
