<?php


function checkExistThenGet($id, $Model, $extraQuery = [], $errorMessage = ''){
    try{
        $id = (int)$id;
        if (is_int($id)) {
            $query = ['id' , '=' , $id];
            if(!empty($extraQuery)){
                $query = $extraQuery;
            }
            //error_log(json_encode($query));
            $model = $Model::where([$query])->first();
            //error_log(json_encode($model));
            if (isset($model))
                return $model;
            else
            abort(404);
    
            
        }
        abort(404);
    }catch(\PDOException $e){//HttpResponseExeption
        return ApiError($e->getCode(),$e->getMessage());
    }
};
function checkEmailExist($email, $Model, $extraQuery = [], $errorMessage = ''){
    try{
        
        $query = ['email' , '=' , $email];
        if(!empty($extraQuery)){
            $query = $extraQuery;
        }
        $model = $Model::where([$query])->first();
        if (isset($model))
            return $model;
        else
        abort(404);
    }catch(\PDOException $e){//HttpResponseExeption
        return ApiError($e->getCode(),$e->getMessage());
    }
};



?>