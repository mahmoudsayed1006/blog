<?php

function ApiError($code= 500,$message=""){
    try{
        if($code == 404){
            $message =__('lang.NotFound');
        }
        if($code == 401){
            $message = __('lang.Unauthorized');
        }
        if($code == 403){
            $message = __('lang.notAllow');
        }
        if($code == 500 && $message ==""){
            $message = __('lang.InternalServerError');
        }
        if($code == 400){
            $message = __('lang.BadRequest');
        }
        $response = [
            'success' => false,
            'errors' => array(
                [
                    'msg' => $message,
                ]
            )
        ];
        return response()->json($response, $code);
    }catch(Exception $e){
        ApiError($e->getCode(),$e->getMessage());
    }
}

?>