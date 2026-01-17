<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class WithdrawalPeriod extends Model
{
    use GlobalStatus;

    protected $fillable = [
        'withdraw_method_id',
        'start_date',
        'end_date',
        'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'status' => 'boolean',
    ];

    /**
     * Get the withdraw method that owns the period.
     */
    public function withdrawMethod()
    {
        return $this->belongsTo(WithdrawMethod::class);
    }

    /**
     * Check if current date is within any active period for a withdraw method.
     * Compares only month and day (ignores year) to make periods recurrent/perpetual.
     *
     * @param int $withdrawMethodId
     * @return bool
     */
    public static function isWithinPeriod($withdrawMethodId)
    {
        $today = now();
        $currentMonthDay = (int) $today->format('md'); // e.g., 215 for Feb 15

        $periods = self::where('withdraw_method_id', $withdrawMethodId)
            ->where('status', 1)
            ->get();

        foreach ($periods as $period) {
            $startMonthDay = (int) $period->start_date->format('md');
            $endMonthDay = (int) $period->end_date->format('md');

            // Handle periods that cross year boundary (e.g., Dec 15 - Jan 15)
            if ($startMonthDay <= $endMonthDay) {
                // Normal period (e.g., Feb 10 - Feb 15)
                if ($currentMonthDay >= $startMonthDay && $currentMonthDay <= $endMonthDay) {
                    return true;
                }
            } else {
                // Period crosses year boundary (e.g., Dec 20 - Jan 10)
                if ($currentMonthDay >= $startMonthDay || $currentMonthDay <= $endMonthDay) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get all active periods for a withdraw method.
     *
     * @param int $withdrawMethodId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getActivePeriods($withdrawMethodId)
    {
        return self::where('withdraw_method_id', $withdrawMethodId)
            ->where('status', 1)
            ->orderBy('start_date')
            ->get();
    }
}
