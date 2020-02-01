<?php


namespace App\Helpers;

//An interface for handling differant failure checks for each service
interface FailureCheck { 
    public static function checkFailure($params); 

 } 


?>