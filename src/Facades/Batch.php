<?php

namespace OANNA\NotificationBatcher\Facades;

use Illuminate\Support\Facades\Facade;
use OANNA\NotificationBatcher\PendingBatch;

/**
 * @method static PendingBatch to($notifiable);
 */
class Batch extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \OANNA\NotificationBatcher\Batch::class;
    }
}