<?php

namespace App\Http\Controllers;

use App\Transfer;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
       return Transfer::createTransfer($request);

    }
}
