<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use GlobalStatus;

    protected $fillable = [
        'project_id',
        'name',
        'description',
        'image',
        'pdf',
        'minimum',
        'maximum',
        'fixed_amount',
        'interest',
        'interest_type',
        'time_setting_id',
        'capital_back',
        'lifetime',
        'repeat_time',
        'compound_interest',
        'hold_capital',
        'featured',
        'testing',
        'days_to_init',
        'capital_months_return',
        'interest_distribution',
        'status'
    ];

    protected $casts = [
        'interest_distribution' => 'array'
    ];

    /**
     * Get the project that owns the plan.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function invests()
    {
        return $this->hasMany(Invest::class);
    }

    public function timeSetting()
    {
        return $this->belongsTo(TimeSetting::class);
    }
}
