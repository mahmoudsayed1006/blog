<?php
function uploadFile($file){
    try{
        $image = $file;
        $imageName = time().'.' . $image->extension();

        $image->move(public_path('storage') , $imageName);
        $url = 'http://'.'localhost:8000/storage/'.$imageName;
        return $url;
    }catch(Exception $e){
        return ApiError($e->getCode(),$e->getMessage());
    }
}
function uploadFiles($files){
    try{
        $sliderImages = array();
        foreach($files as $image){
            $imageName = time().'.' . $image->extension();
            $image->move(public_path('storage') , $imageName);
            $url = 'http://'.'localhost:8000/storage/'.$imageName;
            array_push($sliderImages, $url);
        }
        return $sliderImages;
    }catch(Exception $e){
        return ApiError($e->getCode(),$e->getMessage());
    }
}
?>