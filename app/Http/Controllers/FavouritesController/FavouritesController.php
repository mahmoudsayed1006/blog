<?php
   
namespace App\Http\Controllers\FavouritesController;
   
use Illuminate\Http\Request;
use App\Http\Controllers\SharedController\SharedController as SharedController;
use App\Models\Favourite\Favourite;
use App\Models\Post\Post;
use App\Models\User\User;

use App\Models\Notif\Notif;
use Illuminate\Support\Facades\Auth;
use Validator;

class FavouritesController extends SharedController
{
    
    # add Favourites api
    public function addFavourite(Request $req,$postId){
        try{
            $post = checkExistThenGet($postId,Post::class);
            $user = auth()->user();
            $validatedBody = [
                "user_id" => $user->id,
                "post_id" => $postId,
            ];
            if(is_null(Favourite::where([["user_id","=", $user->id],["post_id","=",$postId],["deleted","=",0]])->first())){
                $fav = $user->favourites;
                if(!in_array ($postId, $fav)){
                    array_push($fav, $postId);
                    $favourite = Favourite::create($validatedBody);
                    User::where('id',$user->id)->update(['favourites'=>$fav]);
                    #notification
                    sendNotification([
                        "targetUser" => $post->user_id, 
                        "fromUser" => $user->id, 
                        "text" => 'New Post Update',
                        "subject" => $postId,
                        "subjectType" => 'new favourite on your post',
                        "info" =>'FAVOURITE'
                    ]);
                    $notif = [
                        "description_en" => 'Someone add you to his favourite list',
                        "description_ar" => 'اضافك شخص ماالى قائمته المفضله',
                        "title_ar" => "لديك اعجاب جديد",
                        "title_en" => "New Like",
                        "type" => "FAVOURITE",
                        "target_id"=>$post->user_id,
                        "resource_id"=>$user->id
                    ];
                    $notifs = Notif::create($notif);
                    $response['data'] =  $favourite;
                    return $this->sendResponse($response);
                }else{
                    return ApiError(500,__("lang.post.found"));
                }

            }
            
        }catch(Exception $e){
            return ApiError($e->getCode(),$e->getMessage());
        }
        
    }
    # Get Favourites Pagenation
    public function getFavourites(Request $req) {
        try{
            $limit = $req->query('limit')??20;
            $page = $req->query('page')??1;
            $query = [
                ['deleted','=',0]
            ];
            if($req->query('post')){
                array_push($query,['post_id','=',$req->query('post')]);
            }
            if($req->query('user')){
                array_push($query,['user_id','=',$req->query('user')]);
            }
            $Favourites = Favourite::where($query)->paginate($limit,['*'],'page',$page);
            $FavouritesCount = Favourite::where($query)->count();
            $pageCount = ceil($FavouritesCount / $limit);
            return ApiResponse($Favourites,$pageCount);
        }catch(Exception $e){
            return ApiError($e->getCode(),$e->getMessage());
        }
    }
    # Get Favourites without pagenation Pagenation
    public function findFavourites(Request $req) {
        try{
            $query = [
                ['deleted','=',0]
            ];
            if($req->query('post')){
                array_push($query,['post_id','=',$req->query('post')]);
            }
            if($req->query('user')){
                array_push($query,['user_id','=',$req->query('user')]);
            }
            $Favourites = Favourite::where($query)->get();
            $response['data'] =  $Favourites;
            return $this->sendResponse($response);
        }catch(Exception $e){
            return ApiError($e->getCode(),$e->getMessage());
        }
    }
    public function deleteFavourite($postId){
        try{
            $user = auth()->user();
            if(is_null(Favourite::where([["user_id","=", $user->id],["post_id","=",$postId],["deleted","=",0]])->first())){
                return ApiError(500,__("lang.post.notFound"));
            }else{
                $favourites = Favourite::where([["user_id","=", $user->id],["post_id","=",$postId],["deleted","=",0]])->first();
                if ($favourites->user_id != $user->id)
                    return ApiError(500, __('notAllow'));
                $favourites->deleted = 1;
                $favourites->save();
                if(in_array ($postId, $user->favourites)){
                    $userFavourites = array_diff($user->favourites,[$postId]);
                    User::where('id',$user->id)->update(['favourites'=>$userFavourites]);
                }
                return response()->json(['success' => 'true'], 200);
            }
            
        }catch(Exception $e){
            return ApiError($e->getCode(),$e->getMessage());
        }
    }
}
