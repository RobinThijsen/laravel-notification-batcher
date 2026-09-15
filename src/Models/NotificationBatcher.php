<?php

namespace OANNA\NotificationBatcher\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use OANNA\NotificationBatcher\Enums\NotificationBatcherStatus;

/**
 * @property string $id
 * @property string $notifiable_type
 * @property mixed $notifiable_id
 * @property string $batch_class
 * @property NotificationBatcherStatus $status
 * @property int $count
 * @property string $payload
 * @property CarbonInterface|null $started_at
 * @property CarbonInterface|null $processed_at
 * @property CarbonInterface|null $failed_at
 * @property CarbonInterface $created_at
 * @property CarbonInterface|null $updated_at
 */
class NotificationBatcher extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'batch_class',
        'payload',
    ];

    protected $casts = [
        'status' => NotificationBatcherStatus::class,
        'started_at' => 'datetime',
        'processed_at' => 'datetime',
        'failed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeOfBatch($query, $batchClass)
    {
        return $query->where('notification_batchers.batch_class', $batchClass);
    }

    public function scopeWaiting($query)
    {
        return $query->where('notification_batchers.status', NotificationBatcherStatus::WAITING);
    }

    public function notifiable()
    {
        return $this->morphTo();
    }

    /**
     * Marks the batch as pending.
     *
     * @return bool
     */
    public function pending()
    {
        return $this->switchStatus(NotificationBatcherStatus::PENDING);
    }

    /**
     * Marks the batch as failed.
     *
     * @return bool
     */
    public function failed()
    {
        return $this->switchStatus(NotificationBatcherStatus::FAILED);
    }

    /**
     * Marks the batch as processed. If the 'mustDeleteAfterProcessed' option is enabled, the batch will be deleted.
     *
     * @return bool|null
     */
    public function processed()
    {
        if (app('batcher')->mustDeleteAfterProcessed()) return $this->delete();
        return $this->switchStatus(NotificationBatcherStatus::PROCESSED);
    }

    /**
     * Updates the status of the current instance and sets corresponding timestamps based on the status value.
     *
     * @param NotificationBatcherStatus $status The new status to switch to. Possible values are 'PENDING', 'PROCESSED', and 'FAILED'.
     * @return bool Returns true if the instance was successfully saved, otherwise false.
     */
    private function switchStatus($status)
    {
        $this->status = $status;

        switch ($status) {
            case NotificationBatcherStatus::PENDING:
                $this->started_at = now();
                break;
            case NotificationBatcherStatus::PROCESSED:
                $this->processed_at = now();
                break;
            case NotificationBatcherStatus::FAILED:
                $this->failed_at = now();
                break;
            default:
                break;
        }

        return $this->save();
    }
}