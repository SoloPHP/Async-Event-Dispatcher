# Solo Async Event Dispatcher

[![Latest Version on Packagist](https://img.shields.io/packagist/v/solophp/async-event-dispatcher.svg?style=flat-square)](https://packagist.org/packages/solophp/async-event-dispatcher)
[![License](https://img.shields.io/github/license/solophp/async-event-dispatcher.svg?style=flat-square)](LICENSE)
[![PHP Version](https://img.shields.io/packagist/php-v/solophp/async-event-dispatcher.svg?style=flat-square)](https://packagist.org/packages/solophp/async-event-dispatcher)

Async event dispatcher for Solo: dispatcher, worker, registry, serializer, and pluggable adapters (in-memory, Solo Task Queue).

## Install

```bash
composer require solophp/async-event-dispatcher
# Optional for DB-backed queue:
composer require solophp/task-queue
```

## Usage (in-memory)

```php
use Solo\AsyncEventDispatcher\{ReferenceListenerRegistry, ListenerReference, AsyncEventDispatcher, Worker};
use Solo\AsyncEventDispatcher\Adapter\InMemoryAdapter;

final class UserRegistered { public function __construct(public string $username) {} }
final class SendWelcomeEmail { public function __invoke(UserRegistered $e): void { echo "sent:" . $e->username . "\n"; } }

$registry = new ReferenceListenerRegistry();
$registry->addReference(UserRegistered::class, new ListenerReference(SendWelcomeEmail::class));

$adapter = new InMemoryAdapter();
$publisher = new AsyncEventDispatcher($registry, $adapter);
$worker = new Worker($adapter);

$publisher->dispatch(new UserRegistered('john'));
$worker->run();
```

## Usage (Solo Task Queue)

```php
use Solo\AsyncEventDispatcher\Adapter\SoloTaskQueueAdapter;
use Solo\AsyncEventDispatcher\{AsyncEventDispatcher, Worker};
use Solo\TaskQueue\TaskQueue;

$pdo = new PDO('sqlite:' . __DIR__ . '/var/queue.sqlite');
$queue = new TaskQueue($pdo);
$queue->install();

$adapter = new SoloTaskQueueAdapter($queue);
$publisher = new AsyncEventDispatcher($registry, $adapter);
$worker = new Worker($adapter);

$publisher->dispatch(new UserRegistered('john'));
$worker->run();
```

## License

MIT


