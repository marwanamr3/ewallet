<?php

namespace App\Http\Controllers;

use App\Service;
use Illuminate\Http\Request;

use Validator;

class ServiceController extends Controller
{
   
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $query = $request['key']; // <-- Change the query for testing.

        $services = Service::search($query)->get();
    
        return $services;
    }

    public function store(Request $request)
    {
        return Service::createService($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function show(Service $service)
    {
        return Service::showById($service);
    }

  
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Service $service)
    {
        return Service::updateById($request, $service);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function destroy(Service $service)
    {
        return Service::deleteById($service);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function subscribeUser(Service $service)
    {
        return Service::subscribeUser($service);
    }
}
