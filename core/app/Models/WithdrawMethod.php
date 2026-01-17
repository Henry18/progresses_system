<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class WithdrawMethod extends Model
{
    use GlobalStatus;

    protected $casts = [
        'user_data' => 'object',
    ];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    /**
     * Get the withdrawal periods for this method.
     */
    public function withdrawalPeriods()
    {
        return $this->hasMany(WithdrawalPeriod::class);
    }

    /**
     * Get active withdrawal periods for this method.
     */
    public function activeWithdrawalPeriods()
    {
        return $this->hasMany(WithdrawalPeriod::class)->where('status', 1);
    }

    /**
     * Check if current date is within an allowed withdrawal period.
     *
     * @return bool
     */
    public function isWithinWithdrawalPeriod()
    {
        return WithdrawalPeriod::isWithinPeriod($this->id);
    }
}
