<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = ['status', "customer_id", "service_id"];

    public function customer()
    {
        return $this->belongsTo('App\Customer');
    }

    public function service()
    {
        return $this->belongsTo('App\Service');
    }

    public function serviceBill()
    {
        return $this->hasOne('App\ServiceBill', 'subscription_id');
    }
}
