<?php

declare(strict_types=1);

namespace Solo\TaskQueue;

interface TaskQueueInterface
{
    public function install(): void;

    public function addTask(
        string $name,
        array $payload,
        ?\DateTimeImmutable $scheduledAt = null,
        ?\DateTimeImmutable $expiresAt = null
    ): int;

    public function processPendingTasks(callable $callback, int $limit = 10, ?string $onlyType = null): void;
}


