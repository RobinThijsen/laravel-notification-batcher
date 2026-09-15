<?php

namespace RobinThijsen\NotificationBatcher\Facades;

use Illuminate\Support\Facades\Facade;
use RobinThijsen\NotificationBatcher\PendingBatch;

/**
 * @method static PendingBatch to($notifiable);
 */
class Batch extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \RobinThijsen\NotificationBatcher\Batch::class;
    }
}