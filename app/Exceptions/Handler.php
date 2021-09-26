<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Illuminate\Support\Facades\App;
use Illuminate\Database\QueryException;
class Handler extends ExceptionHandler
{
   /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */

    public function register()
    {
        $this->renderable(function (NotFoundHttpException $e){
            return response()->json([
                'success' => false,
                'errors' => array(
                    [
                        'msg' =>  __('lang.notFound'),
                    ]
                )
            ], 404);
        });
        $this->renderable(function (MethodNotAllowedHttpException $e){
            return response()->json([
                'success' => false,
                'errors' => array(
                    [
                        'msg' => __('lang.notFound'),
                    ]
                )
            ], 404);
        });
        $this->renderable(function (HttpException $e) {
            
            return response()->json([
                'success' => false,
                'errors' => array(
                    [
                        'msg' => $e->getMessage() ,
                    ]
                )
            ], 500);
        });
        $this->renderable(function (Exception $e) {
            if(app()->environment() === 'production') {
            return response()->json([
                'success' => false,
                'errors' => array(
                    [
                        'msg' => $e->getMessage() ,
                    ]
                )
            ], 500);
        }
        });
        $this->renderable(function (QueryException $e) {
            return response()->json([
                'success' => false,
                'errors' => array(
                    [
                        'msg' => $e->getMessage() ,
                    ]
                )
            ], 500);
        });
        
        
       
        
        
    }
    
  

}


