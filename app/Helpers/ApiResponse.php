<?php

namespace App\Helpers;



class ApiResponse
{
    static function error($errorMessages) {
        return ["errors"=>$errorMessages];
    }
    static function success($message)
    {
        return [
            'message' => $message
        ];
    }
}
