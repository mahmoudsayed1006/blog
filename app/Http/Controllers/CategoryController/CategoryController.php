<?php
   
namespace App\Http\Controllers\CategoryController;
   
use Illuminate\Http\Request;
use App\Http\Controllers\SharedController\SharedController as SharedController;
use App\Models\Category\Category;
use Illuminate\Support\Facades\Auth;
use Validator;

class CategoryController extends SharedController
{
    
    # add category api
    public function addCategory(Request $req){
        try{
            $rules = [
                'name_ar' => ['required'],
                'name_en' => ['required'],
                'img' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ];
            $validated = Validator::make($req->all(), $rules, $messages = [
                'name_ar.required' => __('lang.name_ar.required'),
                'name_en.required' => __('lang.name_en.required'),
                'img' =>__('lang.img.invalid'),
                'img.required' => __('lang.img.required'),
            ]);
            $validatedBody =  $this->validateBody($validated,$req->input());
            if($validated->fails()){
                return $validatedBody;
            }else{
                if($req->hasFile('img'))
                    $validatedBody['img'] = uploadFile($req->file('img'));
                $category = Category::create($validatedBody);
                $response['data'] =  $category;
                return $this->sendResponse($response);
            }
        }catch(Exception $e){
            return ApiError($e->getCode(),$e->getMessage());
        }
        
    }
    # Get Categories Pagenation
    public function getCategories(Request $req) {
        try{
            $limit = $req->query('limit')??20;
            $page = $req->query('page')??1;
            $categories = Category::paginate($limit,['*'],'page',$page);
            $categoriesCount = Category::count();
            $pageCount = ceil($categoriesCount / $limit);
            return ApiResponse($categories,$pageCount);
        }catch(Exception $e){
            return ApiError($e->getCode(),$e->getMessage());
        }
    }
    # Get Categories without pagenation Pagenation
    public function findCategories(Request $req) {
        try{
            $categories = Category::all();
            $response['data'] =  $categories;
            return $this->sendResponse($response);
        }catch(Exception $e){
            return ApiError($e->getCode(),$e->getMessage());
        }
    }
    public function updateCategory(Request $req,$id){
        try{
            $rules = [
                'name_ar' => ['required'],
                'name_en' => ['required'],
                'img' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ];
            $validated = Validator::make($req->all(), $rules, $messages = [
                'name_ar.required' => __('lang.name_ar.required'),
                'name_en.required' => __('lang.name_en.required'),
                'img' =>__('lang.img.invalid'),
                
            ]);
            $validatedBody =  $this->validateBody($validated,$req->input());
            if($validated->fails()){
                return $validatedBody;
            }else{
                
                if($req->hasFile('img'))
                    $validatedBody['img'] = uploadFile($req->file('img'));
                Category::where('id',$id)->update($validatedBody);
                $response['data'] =  Category::where('id',$id)->first();
                return $this->sendResponse($response);
            }
        }catch(Exception $e){
            return ApiError($e->getCode(),$e->getMessage());
        }

    }
    public function getCategoryById($id){
        try{
            $category = checkExistThenGet($id,Category::class);
            return response()->json(['success'=>true, 'category'=>$category], 200);
        }catch(Exception $e){
            return ApiError($e->getCode(),$e->getMessage());
        }
    }
    public function deleteCategory($id){
        try{
            $user = auth()->user();
            if (!in_array($user->type, ['ADMIN'])) 
                return ApiError(403,__('lang.notAllow'));
            checkExistThenGet($id,Category::class);
            $data = Category::find($id)->delete();
            return response()->json(['success' => 'true'], 200);
        }catch(Exception $e){
            return ApiError($e->getCode(),$e->getMessage());
        }
    }
}
