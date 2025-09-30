<?php

/**
 * Solo JobQueue Adapter
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

use Psr\Container\ContainerInterface;
use Solo\AsyncEventDispatcher\EventMessage;
use Solo\Contracts\JobQueue\JobQueueInterface;

/**
 * Adapter for using JobQueue as async backend for AsyncEventDispatcher
 *
 * @category Adapter
 * @package  Solo\AsyncEventDispatcher\Adapter
 * @author   Solo <info@example.com>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/example/async-event-dispatcher
 */
final readonly class SoloJobQueueAdapter implements AsyncAdapterInterface
{
    /**
     * Constructor
     *
     * @param JobQueueInterface  $jobQueue  Job queue instance
     * @param ContainerInterface $container DI container
     */
    public function __construct(
        private JobQueueInterface $jobQueue,
        private ContainerInterface $container
    ) {
    }

    /**
     * Enqueue event message
     *
     * @param EventMessage $message Event message to enqueue
     *
     * @return void
     */
    public function enqueue(EventMessage $message): void
    {
        AsyncEventJob::setContainer($this->container);

        $job = new AsyncEventJob(
            $message->eventClass,
            $message->eventPayload,
            $message->listenerClass,
            $message->listenerMethod
        );

        $this->jobQueue->push($job, 'async_event');
    }

    /**
     * Consume jobs from queue
     *
     * @param callable $handler Job handler
     *
     * @return void
     */
    public function consume(callable $handler): void
    {
        $this->jobQueue->processJobs(50, 'async_event');
    }
}
