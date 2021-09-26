<?php
   
namespace App\Http\Controllers\RateController;
   
use Illuminate\Http\Request;
use App\Http\Controllers\SharedController\SharedController as SharedController;
use App\Models\Rate\Rate;
use App\Models\Post\Post;

use Illuminate\Support\Facades\Auth;
use Validator;

class RateController extends SharedController
{
    
    # add Rate api
    public function addRate(Request $req,$postId){
        try{
            $rules = [
                'comment' => ['required'],
                'rate' => ['required','min:0','max:5'],
            ];
            $validated = Validator::make($req->all(), $rules, $messages = [
                'comment.required' => __('lang.comment.required'),
                'rate.required' => __('lang.rate.required'),
                'rate.min' => __('lang.rate.invalid'),
                'rate.max' => __('lang.rate.invalid')
            ]);
            $validatedBody =  $this->validateBody($validated,$req->input());
            if($validated->fails()){
                return $validatedBody;
            }else{
                
                $validatedBody['user_id'] = auth()->user()->id;
                $validatedBody['post_id']= $postId;
                $post = checkExistThenGet($postId,Post::class);
                $newRate = $post->rateCount + (int)$validatedBody['rate'];
                $post->rateCount = $newRate;
                $post->rateNumbers = $post->rateNumbers + 1;
                $totalDegree = $post->rateNumbers * 5; 
                $degree = $newRate * 100;
                $ratePrecent = $degree / $totalDegree;
                $rate = $ratePrecent / 20;
                $post->rate = ceil((int)$rate);
                $post->save();
                $rate = Rate::create($validatedBody);
                $response['data'] =  $rate;
                return $this->sendResponse($response);
            }
        }catch(Exception $e){
            return ApiError($e->getCode(),$e->getMessage());
        }
        
    }
    # Get Rates Pagenation
    public function getRates(Request $req) {
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

            $rates = Rate::where($query)
                    ->orderBy('created_at', 'asc')
                    ->paginate($limit,['*'],'page',$page);
            $ratesCount = Rate::where($query)->count();
            $pageCount = ceil($ratesCount / $limit);
            return ApiResponse($rates,$pageCount);
        }catch(Exception $e){
            return ApiError($e->getCode(),$e->getMessage());
        }
    }
    # Get Rates without pagenation Pagenation
    public function findRates(Request $req) {
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
            $rates = Rate::where($query)->get();
            $response['data'] =  $rates;
            return $this->sendResponse($response);
        }catch(Exception $e){
            return ApiError($e->getCode(),$e->getMessage());
        }
    }
    public function updateRate(Request $req,$id){
        try{
            $rules = [
                'comment' => ['required'],
                'rate' => ['required','min:0','max:5'],
            ];
            $validated = Validator::make($req->all(), $rules, $messages = [
                'comment.required' => __('lang.comment.required'),
                'rate.required' => __('lang.rate.required'),
                'rate.min' => __('lang.rate.invalid'),
                'rate.max' => __('lang.rate.invalid')
            ]);
            $validatedBody =  $this->validateBody($validated,$req->input());
            if($validated->fails()){
                return $validatedBody;
            }else{
                $theRate = checkExistThenGet($id,Rate::class);
                $post = checkExistThenGet($theRate->post_id,Post::class);
                $newRate = $post->rateCount + ((int)$validatedBody['rate'] - (int)$theRate['rate']);
                $post->rateCount = $newRate;
                $totalDegree = $post->rateNumbers * 5; 
                $degree = $newRate * 100;
                $ratePrecent = $degree / $totalDegree;
                $rate = $ratePrecent / 20;
                $post->rate = ceil((int)$rate);
                $post->save();
                Rate::where('id',$id)->update($validatedBody);
                $response['data'] =  Rate::where('id',$id)->first();
                return $this->sendResponse($response);
            }
        }catch(Exception $e){
            return ApiError($e->getCode(),$e->getMessage());
        }

    }
    public function deleteRate($id){
        try{
            $theRate = checkExistThenGet($id,Rate::class);
            $post = checkExistThenGet($theRate->post_id,Post::class);
            $newRate = $post->rateCount - (int)$theRate->rate;
            $post->rateCount = $newRate;
            $post->rateNumbers = $post->rateNumbers - 1;
            $totalDegree = $post->rateNumbers * 5; 
            $degree = $newRate * 100;
            if($degree != 0){
                $ratePrecent = $degree / $totalDegree;
                $rate = $ratePrecent / 20;
                $post->rate = ceil((int)$rate);
            }else{
                $post->rate = 0;
            }
            $post->save();
            $theRate->deleted = 0;
            $theRate->save();
            //$data = Rate::find($id)->delete();
            return response()->json(['success' => 'true'], 200);
        }catch(Exception $e){
            return ApiError($e->getCode(),$e->getMessage());
        }
    }
}
