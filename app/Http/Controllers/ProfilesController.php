<?php

namespace App\Http\Controllers;

use App\Profile;
use Illuminate\Http\Request;

class ProfilesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function viewProfile()
    {
        return Profile::viewProfile();
    }

    public function updateProfile(Request $request)
    {
        return Profile::updateProfile($request);
    }

    public function deleteProfile()
    {
        return Profile::deleteProfile();
    }

}
