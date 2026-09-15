<?php

namespace RobinThijsen\NotificationBatcher;

use Carbon\CarbonInterface;
use Carbon\CarbonInterval;

class Batcher
{
    public function resolveQueue($queue)
    {
        return $queue ?? config('notification-batcher.queue', 'batch');
    }

    public function resolveDelay($delay)
    {
        $now = now();

        if ($delay instanceof CarbonInterface) {
            return $delay;
        }

        if ($delay instanceof CarbonInterval) {
            return $now->add($delay);
        }

        if (is_numeric($delay)) {
            $delay = CarbonInterval::seconds($delay);
        }

        if (empty($delay)) {
            $delay = config('notification-batcher.delay', CarbonInterval::minutes(30));
        }

        return $now->add($delay);
    }

    public function resolveDirectoryPath()
    {
        return base_path($this->resolveRelativePath());
    }

    public function resolveRelativePath()
    {
        return config('notification-batcher.directory_path', 'app/Batches');
    }

    public function resolveStubPath()
    {
        $custom = config('notification-batcher.stub_path');
        return $custom ? base_path($custom) : __DIR__ . '/../stubs/Batch.php.stub';
    }

    public function mustDeleteAfterProcessed()
    {
        return config('notification-batcher.delete_after_processed', true);
    }
}