<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceBill extends Model
{
    public function Bill()
    {
        return $this->morphOne('App\Bill', 'billable');
    }
}
