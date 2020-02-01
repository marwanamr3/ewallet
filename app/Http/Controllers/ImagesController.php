<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Helpers\ApiResponse;
class ImagesController extends Controller
{
    //
    public function getImage(Request $request, $path)
    {
        try {
            $path = storage_path() . '/app/images/' . $path;
            return Response::download($path);
        }
        catch (\Exception $e)
        {
            return response()->json(ApiResponse::error(["data" => "Image doesn't exist"]), 404);
        }

    }
}
