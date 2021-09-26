<?php
   
namespace App\Http\Controllers\PostController;
   
use Illuminate\Http\Request;
use App\Http\Controllers\SharedController\SharedController as SharedController;
use App\Models\Post\Post;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class PostController extends SharedController
{
    
    # add Post api
    public function addPost(Request $req) {
        try{
            $rules = [
                'title_ar' => ['required'],
                'title_en' => ['required'],
                'description_ar' => ['required'],
                'description_en' => ['required'],
                'category_id' => ['required','numeric'],
                'user_id' => ['required','numeric'],
            ];
            $validated = Validator::make($req->all(), $rules, $messages = [
                'title_ar.required' => __('lang.title_ar.required'),
                'title_en.required' => __('lang.title_en.required'),
                'description_ar.required' => __('lang.description_ar.required'),
                'description_en.required' => __('lang.description_en.required'),
                'category_id.required' => __('lang.category_id.required'),
                'user_id.required' => __('lang.user_id.required'),
                'category_id.numeric' => __('lang.category_id.numeric'),
                'user_id.numeric' => __('lang.user_id.numeric'),
            ]);
            $validatedBody =  $this->validateBody($validated,$req->input());
            if($validated->fails()){
                return $validatedBody;
            }else{
                $post = Post::create($validatedBody);
                $response['data'] =  $post;
                return $this->sendResponse($response);
            }
        }catch(Exception $e){
            return ApiError($e->getCode(),$e->getMessage());
        }
        
    }
    # Get Posts Pagenation
    public function getPosts(Request $req) {
        try{
            $limit = $req->query('limit')??20;
            $page = $req->query('page')??1;
            $query = [
                ['deleted','=',0]
            ];
            if($req->query('post')){
                array_push($query,['category_id','=',$req->query('category')]);
            }
            if($req->query('user')){
                array_push($query,['user_id','=',$req->query('user')]);
            }
            //getPagenation is acustom function 
            //$Posts = Post::getPagenation($limit,$page,$query);
            $Posts = Post::where($query)->paginate($limit,['*'],'page',$page);
            error_log($Posts);
            $PostsCount = Post::where($query)->count();
            $pageCount = ceil($PostsCount / $limit);
            return ApiResponse($Posts,$pageCount);
        }catch(Exception $e){
            return ApiError($e->getCode(),$e->getMessage());
        }
    }
    # Get Posts without pagenation Pagenation
    public function findPosts(Request $req) {
        try{
            $query = [
                ['deleted','=',0]
            ];
            if($req->query('post')){
                array_push($query,['category_id','=',$req->query('category')]);
            }
            if($req->query('user')){
                array_push($query,['user_id','=',$req->query('user')]);
            }
            //$posts = Post::getAll($query);
            $posts = Post::where($query)->get();
            $response['data'] = $posts; 
            return $this->sendResponse($response);
        }catch(Exception $e){
            return ApiError($e->getCode(),$e->getMessage());
        }
    }
    public function updatePost(Request $req,$id) {
        try{
            $rules = [
                'title_ar' => ['required'],
                'title_en' => ['required'],
                'description_ar' => ['required'],
                'description_en' => ['required'],
                'category_id' => ['required','numeric'],
                'user_id' => ['required','numeric'],
            ];
            $validated = Validator::make($req->all(), $rules, $messages = [
                'title_ar.required' => __('lang.title_ar.required'),
                'title_en.required' => __('lang.title_en.required'),
                'description_ar.required' => __('lang.description_ar.required'),
                'description_en.required' => __('lang.description_en.required'),
                'category_id.required' => __('lang.category_id.required'),
                'user_id.required' => __('lang.user_id.required'),
                'category_id.numeric' => __('lang.category_id.numeric'),
                'user_id.numeric' => __('lang.user_id.numeric'),
            ]);
            $validatedBody =  $this->validateBody($validated,$req->input());
            if($validated->fails()){
                return $validatedBody;
            }else{
                Post::where('id',$id)->update($validatedBody);
                $response['data'] =  Post::where('id',$id)->first();
                return $this->sendResponse($response);
            }
        }catch(Exception $e){
            ApiError($e->getCode(),$e->getMessage());
        }
    }
    public function getPostById($id){
        try{
            $post = checkExistThenGet($id,Post::class);
            return response()->json(['success'=>true, 'post'=>$post], 200);
        }catch(Exception $e){
            return ApiError($e->getCode(),$e->getMessage());
        }
    }
    public function deletePost($id){
        try{
            $user = auth()->user();
            if (!in_array($user->type, ['ADMIN'])) 
                return  ApiError(403,__('lang.notAllow'));
            checkExistThenGet($id,Post::class);
            $data = Post::find($id)->delete();
            return response()->json(['success' => 'true'], 200);
        }catch(Exception $e){
            return ApiError($e->getCode(),$e->getMessage());
        }
    }
}
