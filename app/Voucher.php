<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{


    protected $fillable = [
        'id',
        'voucher_code',
        'created_at',
        'updated_at',

    ];


    public function Recharge()
    {
        return $this->morphOne('App\Recharge', 'rechargeable');
    }

    static function checkVoucher($voucher_code)
    {
        $voucher = Voucher::where("voucher_code","=",$voucher_code)->get()->first();
        if($voucher)
        {
            if($voucher->valid)
                return [true, $voucher];
            else
                return [false, "Voucher Expired"];
        }
        else
            return [false, "Incorrect Voucher Code"];
    }
}
