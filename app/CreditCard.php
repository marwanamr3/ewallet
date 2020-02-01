<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CreditCard extends Model
{

    protected $fillable = [
        'company_name',
        'expiry_date',
    ];
    public function Recharge()
    {
        return $this->morphOne('App\Recharge', 'rechargeable');
    }
}
