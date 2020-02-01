<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use Validator;

class Profile extends Model
{
    public static function getUserType()
    {
        //get user ID
        $userId = auth()->id();
        //get user type
        $userType = DB::table('users')
            ->select('users.type')
            ->where('users.id', '=', $userId)
            ->get();
        return $userType[0]->type;
    }

    public static function viewProfile()
    {

        $user = auth()->user();

        $userType = Profile::getUserType();
        //get the table to join with based on type
        $userTypeModel = $userType == 'Customer' ? Customer::find($user->id) : Merchant::find($user->id);
        $user = collect($user)->merge($userTypeModel);
        return response()->json($user, 200);
    }

    public static function deleteProfile()
    {
        $user = auth()->user();
        if ($user->delete())
            return response()->json(ApiResponse::success("Deleted Successfully"), 200);
        return response()->json(ApiResponse::error(["data" => "Error deleting user"]), 400);
    }

    public static function updateProfile(Request $request)
    {
        $rules = [
            'username' => 'sometimes|min:3|alpha_dash|unique:users',
            'email' => 'sometimes|email|unique:users',
            'password' => 'sometimes|min:6',
            'first_name' => 'sometimes|string',
            'last_name' => 'sometimes|string',
            'name' => 'sometimes|string',
            'address' => 'sometimes|string',
            'image' => 'sometimes|mimes:jpeg,bmp,png'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return  response()->json(ApiResponse::error($validator->errors()), 400);
        }

        $user = auth()->user();
        $userId = (auth()->id());
        $userType = Profile::getUserType();
        $userTypeModel = $userType == 'Customer' ? Customer::find($userId) : Merchant::find($userId);


        try {
            DB::beginTransaction();

            // update password - if given
            if ($request['password'])
                $request['password'] = bcrypt($request->password);

            // update image - if given (Postman: uses form data not form-urlencoded)
            if ($request->hasFile('image')) {

                $path = $request->file('image')->store('images'); // Folder path is  e-wallet/storage/app/images
                $user->image = explode('/', $path)[1];
            }

            // update user table
            $user->update($request->only(['username', 'email', 'password']));

            // update type table
            $userTypeModel->update($request->only(['first_name', 'last_name', 'name', 'address']));

            // get updated data
            $user = collect($user)->merge($userTypeModel);

            DB::commit();
            return response()->json($user, 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(ApiResponse::error(["data" => "Error updating User"]), 400);
        }
    }
}
