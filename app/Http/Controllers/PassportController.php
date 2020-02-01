<?php

namespace App\Http\Controllers;

use App\User;
use App\Merchant;
use App\Customer;
use http\Env\Response;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\String_;
use Dotenv\Parser;
use Validator;
use Illuminate\Support\Facades\DB;
use App\Helpers\ApiResponse;

class PassportController extends Controller
{
    /**
     * Handles Registration Request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {

        $rules = [
            'username' => 'required|min:3|alpha_dash|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'type' => 'required|in:Customer,Merchant',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return  response()->json(ApiResponse::error($validator->errors()), 400);
        }

        try {
            DB::beginTransaction();
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'type' => $request->type,
            ]);
            //put in helpers class:
            $user_type = array("Customer" => Customer::class, "Merchant" => Merchant::class);
            $user_type[$request->type]::create([
                'id' => $user->id,
            ]);
            $token = $user->createToken('TutsForWeb')->accessToken;
            DB::commit();
            return response()->json(['token' => $token], 201);
        } catch (\Exception $e) {
            DB::rollback();
        }
    }

    /**
     * Handles Login Request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = [
            'username' => $request->username,
            'password' => $request->password
        ];
        if (auth()->attempt($credentials)) {
            $token = auth()->user()->createToken('Personal Access Token')->accessToken;
            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(ApiResponse::error(['data' => 'UnAuthorised User']), 400);
        }
    }

    /**
     * Returns Authenticated User Details
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function user()
    {
        return response()->json(['user' => auth()->user()], 200);
    }


    /**
     * Handles Logout Request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}
