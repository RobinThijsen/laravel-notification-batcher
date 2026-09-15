<?php

namespace RobinThijsen\NotificationBatcher;

use RobinThijsen\NotificationBatcher\Jobs\BatchJob;

class PendingBatch
{
    public function __construct(
        protected $notifiable,
    ) {}

    public function notifyBatch($batch, $delay = null, $queue = null): void
    {
        // get the existing notification batcher for the given batch
        $notificationBatcher = $this->notifiable->notificationBatchers()
            ->ofBatch(get_class($batch))
            ->waiting()
            ->first();

        if (!empty($notificationBatcher)) {
            $notificationBatcher->increment('count');
            return;
        }

        // create a notification batcher for the given batch
        $notificationBatcher = $this->notifiable->notificationBatchers()->create([
            'batch_class' => get_class($batch),
            'payload' => serialize($batch),
        ]);

        // dispatch the job
        BatchJob::dispatch($notificationBatcher->id)
            ->onQueue(app('batcher')->resolveQueue($queue ?? $batch->queue ?? null))
            ->delay(app('batcher')->resolveDelay($delay ?? $batch->delay ?? null));
    }
}