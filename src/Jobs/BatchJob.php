<?php

namespace RobinThijsen\NotificationBatcher\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;

use RobinThijsen\NotificationBatcher\Models\NotificationBatcher;

class BatchJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(
        public $notificationBatcherId,
    ) {}

    public function handle()
    {
        $notificationBatcher = NotificationBatcher::find($this->notificationBatcherId);

        try {
            if (empty($notificationBatcher)) throw new \Exception("The NotificationBatcher associated with the job no longer appears to exist.");

            $notificationBatcher->pending();
            $notificationBatcher->refresh(); // recharge count depuis la DB, après la fermeture de la fenêtre

            $notifiable = $notificationBatcher->notifiable;
            $batch = unserialize($notificationBatcher->payload);

            $batch->handle($notifiable, $notificationBatcher);
            $notificationBatcher->processed();
        }
        catch (\Throwable $e) {
            if (!empty($notificationBatcher)) $notificationBatcher->failed();
            throw $e;
        }
    }
}