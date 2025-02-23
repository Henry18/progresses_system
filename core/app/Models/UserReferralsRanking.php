<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserReferralsRanking extends Model
{
    protected $table = 'user_referrals_rankings';

    protected $fillable = [
        'codigo_referred',
        'codigo_referrer',
        'referral_bonus_times',
        'referral_bonus_times_max',
        'referral_ranking'
    ];

    public function referred()
    {
        return $this->belongsTo(User::class, 'codigo_referred');
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'codigo_referrer');
    }

    public function incrementBonusTimes()
    {
        $this->referral_bonus_times++;
        return $this->save();
    }

    public function updateRanking($newRanking)
    {
        $this->referral_ranking = $newRanking;
        return $this->save();
    }
}
