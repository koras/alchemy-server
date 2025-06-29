<?php

namespace App\Models;

use App\Contracts\Models\AppEventInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppEvent extends Model implements AppEventInterface
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'app_events';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'event_type',
        'event_data',
      //  'ip_address',
     //   'device_info'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'event_data' => 'array',
        'device_info' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Event types constants
     */
    public const EVENT_TYPES = [
        'AD_VIEW' => 'ad_view',
        'LEVEL_START' => 'level_start',
        'LEVEL_COMPLETE' => 'level_complete',
        'LEVEL_FAIL' => 'level_fail',
        'PURCHASE' => 'purchase',
        'HINT_USED' => 'hint_used',
        'IAP_PURCHASE' => 'iap_purchase',
        'APP_LAUNCH' => 'app_launch',
        'APP_CLOSE' => 'app_close',
    ];

    /**
     * Scope a query to filter by event type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('event_type', $type);
    }

    /**
     * Scope a query to filter by date range.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $from
     * @param string $to
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBetweenDates($query, $from, $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    /**
     * Scope a query to filter by device ID.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $deviceId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForDevice($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
