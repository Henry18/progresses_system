<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfitDistribution extends Model
{
    protected $guarded = ['id'];

    public function plan()
    {
        return $this->belongsTo(Plan::class)->withDefault();
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class)->withDefault();
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'distribution_id');
    }

    public function getTypeLabel(): string
    {
        return $this->type === 'equitativo' ? 'Equitativo' : 'Por Porcentaje';
    }
}