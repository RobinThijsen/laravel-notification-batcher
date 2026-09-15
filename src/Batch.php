<?php

namespace OANNA\NotificationBatcher;

use Illuminate\Database\Eloquent\Model;

class Batch
{
    protected $notifiable = null;

    /**
     * Sets the notifiable entity for the current instance.
     *
     * @param Model $notifiable The entity to be notified.
     * @return PendingBatch Returns the current instance.
     */
    public function to($notifiable): PendingBatch
    {
        return new PendingBatch($notifiable);
    }
}