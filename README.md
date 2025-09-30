# Solo Async Event Dispatcher

[![Latest Version on Packagist](https://img.shields.io/packagist/v/solophp/async-event-dispatcher.svg?style=flat-square)](https://packagist.org/packages/solophp/async-event-dispatcher)
[![License](https://img.shields.io/github/license/solophp/async-event-dispatcher.svg?style=flat-square)](LICENSE)
[![PHP Version](https://img.shields.io/packagist/php-v/solophp/async-event-dispatcher.svg?style=flat-square)](https://packagist.org/packages/solophp/async-event-dispatcher)

Async event dispatcher with pluggable adapters.

## Features

- **PSR-14 Compatible**: Implements `Psr\EventDispatcher\EventDispatcherInterface`
- Async dispatch via `$dispatcher->dispatch()`
- Pluggable adapters: `InMemoryAdapter`, `SoloJobQueueAdapter` (with built-in `AsyncEventJob`)
- Worker to consume and execute events
- Reference-based listener registry with priorities
- Customizable serialization via `SerializerInterface`
- Framework-agnostic, minimal setup

## Requirements

- PHP ^8.1
- Optional: `solophp/job-queue` ^1.0 (for `SoloJobQueueAdapter`)

## PSR-14 Compatibility

This library implements the [PSR-14 Event Dispatcher](https://www.php-fig.org/psr/psr-14/) standard, making it compatible with any framework or library that expects an `EventDispatcherInterface`.

```php
use Psr\EventDispatcher\EventDispatcherInterface;
use Solo\AsyncEventDispatcher\AsyncEventDispatcher;

// AsyncEventDispatcher implements EventDispatcherInterface
function processEvent(EventDispatcherInterface $dispatcher, object $event): object
{
    return $dispatcher->dispatch($event);
}

$asyncDispatcher = new AsyncEventDispatcher($registry, $adapter);
$modifiedEvent = processEvent($asyncDispatcher, new UserRegistered('john'));
```

## Install

```bash
composer require solophp/async-event-dispatcher
# Optional for DB-backed queue:
composer require solophp/job-queue
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

$event = $publisher->dispatch(new UserRegistered('john'));
$worker->run();
```

## Usage (Solo Job Queue Adapter)

The `SoloJobQueueAdapter` provides database-backed async event processing with built-in `AsyncEventJob` for seamless integration.

```php
use Solo\AsyncEventDispatcher\Adapter\SoloJobQueueAdapter;
use Solo\AsyncEventDispatcher\{AsyncEventDispatcher, Worker};
use Solo\JobQueue\JobQueue;
use Doctrine\DBAL\DriverManager;

$connection = DriverManager::getConnection([
    'driver' => 'pdo_sqlite',
    'path' => __DIR__ . '/var/queue.sqlite',
]);
$queue = new JobQueue($connection);
$queue->install();

// SoloJobQueueAdapter automatically uses AsyncEventJob internally
$adapter = new SoloJobQueueAdapter($queue, $container);
$publisher = new AsyncEventDispatcher($registry, $adapter);
$worker = new Worker($adapter);

$event = $publisher->dispatch(new UserRegistered('john'));
$worker->run();
```

The adapter automatically handles:
- Event serialization via `AsyncEventJob`
- Job queuing with `'async_event'` type
- Listener resolution through container
- Event deserialization and execution

## Testing

```bash
# Run tests
composer test

# Run code sniffer
composer cs

# Fix code style issues
composer cs-fix
```

## License

This project is open-sourced under the [MIT license](./LICENSE).