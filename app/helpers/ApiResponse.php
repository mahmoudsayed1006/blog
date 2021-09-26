<?php
function ApiResponse($data,$pageCount){
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
        return ApiError($e->getCode(),$e->getMessage());
    }
}
?>