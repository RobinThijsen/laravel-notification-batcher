<?php

namespace RobinThijsen\NotificationBatcher\Concerns;

use Carbon\CarbonInterval;
use RobinThijsen\NotificationBatcher\Facades\Batch;
use RobinThijsen\NotificationBatcher\Models\NotificationBatcher;

trait Batchable
{
    /**
     * Dispatch a job to send a batch of notifications to the notifiable entity.
     * or increment the batch count
     *
     * @param mixed $batch The batch instance to be processed.
     * @param int|null $delay Optional delay time before dispatching the job, in seconds.
     * @param string|null $queue Optional queue name to which the job should be dispatched.
     * @return void
     */
    public function notifyBatch($batch, $delay = null, $queue = null): void
    {
        Batch::to($this)->notifyBatch($batch, $delay, $queue);
    }

    public function notificationBatchers()
    {
        return $this->morphMany(NotificationBatcher::class, 'notifiable');
    }
}