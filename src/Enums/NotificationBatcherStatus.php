<?php

namespace RobinThijsen\NotificationBatcher\Enums;

enum NotificationBatcherStatus: string
{
    case WAITING = 'waiting';
    case PENDING = 'pending';
    case PROCESSED = 'processed';
    case FAILED = 'failed';
}
