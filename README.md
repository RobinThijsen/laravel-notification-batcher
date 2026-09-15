# A Laravel package that simplifies sending notifications via batch.

---

## Installation

```bash
composer require oanna/laravel-notification-batcher
```

Then run the installation command:

```bash
php artisan notification-batcher:install
```

This will publish the config file and the migration, and ask you if you want to run the migration.

You must also run a queue worker on the `batch` queue (or the one you defined in the arguments, class property or config file):

```bash
php artisan queue:work --queue=batch
```

---

## How it works

When `notifyBatch()` is called for the first time for a given notifiable + batch class, the package creates a `NotificationBatcher` record and dispatches a job with a delay. Any subsequent call within that delay simply increments the count. When the job fires, it calls `handle()` on your Batch class with the final count.

---

## Usage

### 1. Add the trait to your notifiable model

```php
use OANNA\NotificationBatcher\Concerns\Batchable;

class User extends Authenticatable
{
    use Batchable;
}
```

### 2. Create a Batch class

```bash
php artisan make:batch NewMessageBatch
```

The generated class has a `handle()` method that receives the notifiable and the `NotificationBatcher` record. Use `$notificationBatcher->count` to know how many notifications were batched.

```php
<?php

namespace App\Batches;

use OANNA\NotificationBatcher\Models\NotificationBatcher;

class NewMessageBatch
{
    public function __construct(
        protected $messageId,
    ) {}

    public function handle($notifiable, NotificationBatcher $notificationBatcher)
    {
        $message = Message::findOrFail($this->messageId);
        
        if ($notificationBatcher->count === 1) {
            $notifiable->notifyNow(new NewMessageNotification($this->message));
        } else {
            $notifiable->notifyNow(new NewMessagesNotification($notificationBatcher->count));
        }
    }
}
```

> We recommend passing only IDs as constructor arguments and retrieving models inside `handle()` method to avoid serialization issues with stale data.

### 3. Send a batch notification

```php
use OANNA\NotificationBatcher\Facades\Batch;

$user->notifyBatch(new NewMessageBatch());

// or

Batch::to($user)->notifyBatch(new NewMessageBatch());
```

You can define a custom delay for the batch by passing a `CarbonInterval` as the second argument:

```php
$user->notifyBatch(new NewMessageBatch(), CarbonInterval::minutes(5));
```

Or by setting the `$delay` property on the Batch class:

```php
use Carbon\CarbonInterval;

class NewMessageBatch
{
    public $delay = null;

    public function __construct()
    {
        $this->delay = CarbonInterval::minutes(10);
    }
}
```

You can also define a custom queue via the third argument:

```php
$user->notifyBatch(new NewMessageBatch(), queue: 'notifications');
```

Or by setting the `$queue` property on the Batch class:

```php
class NewMessageBatch
{
    public $queue = 'notifications';
}
```

> If you want to use a queue other than the default one in the package, don't forget to run a worker for that queue.

---

## Configuration

If you installed the package, you should have
```bash
php artisan vendor:publish --tag="laravel-notification-batcher-config"
```

> If you installed the package using the command `php artisan notification-batcher:install`, you should already have the config file published.

```php
return [
    // Directory where Batch classes are generated
    'directory_path' => 'app/Batches',

    // Path to a custom stub (null = use package default)
    'stub_path' => null,

    // Queue name for batch jobs
    'queue' => 'batch',

    // Default delay before the job fires
    'delay' => \Carbon\CarbonInterval::minutes(30),
    
    // Delete the batch record after the job is processed (true = delete, false = keep)
    'delete_after_processed' => true,
];
```

Priority order for delay: method argument → `$batch->delay` property → config value.

Priority order for queue: method argument → `$batch->queue` property → config value.
