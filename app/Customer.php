<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends User
{
    //
//    public function user()
//    {
//        return $this->morphOne('App\User', 'userable');
//    }


    protected $fillable = [
        'id','first_name','last_name',
    ];



    public function subscriptions()
    {
        return $this->hasMany('App\Subscription');
    }

    public function transactions()
    {
        return $this->hasMany('App\Transaction');
    }

    public function transfer()
    {
        return $this->hasOne('App\Transfer','receiver_id');
    }
}
