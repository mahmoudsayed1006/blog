<?php


namespace App\Http\Controllers\SharedController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;  
use Illuminate\Support\Facades\App;


class SharedController extends Controller
{

    public function sendResponse($result){
        try{
            $result ['success'] = true;
            return response()->json($result, 200);
        }catch(Exception $e){
            ApiError($e->getCode(),$e->getMessage());
        }
    }
    public function validateBody($validated,$body){
        try {
            if($validated->fails()){
                $validations = $validated->errors();
    
                $errors = array();
                foreach ($validations->all() as $val) {
                    $item = array(
                        'msg' => $val,
                        'location' => 'body'
                    );
                    array_push($errors,$item);
                }
                return response()->json(['success' => false,'errors'=>$errors],422);
            }else{
                return $body;
            }
        }catch(Exception $e){
            ApiError($e->getCode(),$e->getMessage());
        }
        
  
    }
    public function ApiError($code= 500,$message=""){
        try{
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
    
    public function ApiResponse($data,$pageCount){
        try{
            $data = [
                'success' => true,
                'data' => $data->items(),
                'page' => $data->currentPage(),
                'pageCount' => $pageCount,
                "limit" => $data->perPage(),
                'totalCount' => $data->total(),
                'links'=>[
                    'self' => $data->url($data->currentPage()),
                    'prev'=>$data->previousPageUrl(),
                    'next'=>$data->nextPageUrl(),
                ],
                
            ];
            return response()->json($data,200);
        }catch(Exception $e){
            ApiError($e->getCode(),$e->getMessage());
        }

        
  
    }
    public function postTransform($data){
        
        $items=[];
        foreach ($data as $item) {
            $title = 'title_'.App::currentLocale();
            $description = 'description_'.App::currentLocale();
           
            array_push($items, [
                'id'=>$item->id, 
                'title'=>$item[$title],
                'description'=>$item[$description],
                'user'=>$item->user,
                'category'=>$item->category,
                'created_at'=>$item->created_at,
                'updated_at'=>$item->updated_at,
            ]);
        }
        
        return $items;
        
    }
    
}