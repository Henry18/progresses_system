<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use GlobalStatus;

    protected $fillable = [
        'name',
        'description',
        'image',
        'pdf',
        'minimum_investment',
        'maximum_investment',
        'days_to_init',
        'featured',
        'testing',
        'status'
    ];

    protected $casts = [
        'minimum_investment' => 'decimal:8',
        'maximum_investment' => 'decimal:8',
        'featured' => 'boolean',
        'testing' => 'boolean',
        'status' => 'boolean'
    ];

    /**
     * Get the plans for the project.
     */
    public function plans()
    {
        return $this->hasMany(Plan::class);
    }

    /**
     * Get active plans for the project.
     */
    public function activePlans()
    {
        return $this->hasMany(Plan::class)->where('status', 1);
    }

    /**
     * Get all investments through plans.
     */
    public function invests()
    {
        return $this->hasManyThrough(Invest::class, Plan::class);
    }

    /**
     * Scope to get featured projects.
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', 1);
    }

    /**
     * Scope to get non-testing projects.
     */
    public function scopeNonTesting($query)
    {
        return $query->where('testing', 0);
    }

    /**
     * Scope to get active projects.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
