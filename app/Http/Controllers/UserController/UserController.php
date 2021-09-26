<?php
   
namespace App\Http\Controllers\UserController;
   
use Illuminate\Http\Request;
use App\Http\Controllers\SharedController\SharedController as SharedController;
use App\Models\User\User;
use App\Models\Notif\Notif;

use Illuminate\Support\Facades\Auth;
use Validator;
//use Illuminate\Routing\Controller as Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\sendMail;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\Rule;

class UserController extends SharedController
{
    
    #Create a new AuthController instance.
    public function __construct(){
        //$this->middleware('auth:api', ['except' => ['login','signUp']]);
    }
    # signUp api
    public function signUp(Request $req){
        try{
            $rules = [
                'name' => ['required','max:255'],
                'phone' => ['required','unique:users'],
                'email' => ['required','unique:users','email'],
                'password' => ['required','min:6','max:16'],
                'confirm_password' => ['required','same:password'],
                'type' => ['required',Rule::in(['USER', 'ADMIN'])],//notIn
                'interests' => ['required'],
                'img' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                //'token' => [],
            ];
            $validated = Validator::make($req->all(), $rules, $messages = [
                'name.required' => __('lang.name.required'),
                'phone.required' => __('lang.phone.required'),
                'password.required' => __('lang.password.required'),
                'type.required' => __('lang.type.required'),
                'interests.required' => __('lang.interests.required'),
                'type.in' => __('lang.type.in'),
                'confirm_password.required' => __('lang.confirm_password.required'),
                'confirm_password.same' => __('lang.confirm_password.same'),
                'phone.unique' => __('lang.phone.unique'),
                'email.unique' => __('lang.email.unique'),
                'img' =>__('lang.img')
            ]);
            $validatedBody =  $this->validateBody($validated,$req->input());
            if($validated->fails()){
                return $validatedBody;
            }else{
                $validatedBody['interests'] = json_decode($req->input('interests'));
                if($req->input('token'))
                    $validatedBody['token'] = [$req->input('token')];
                
                if($req->hasFile('img'))
                    $validatedBody['image'] = uploadFile($req->file('img'));
                $user = User::create($validatedBody);
                $response['user'] =  $user;
                $credentials = [
                    'phone' => $req->input('phone'),
                    'password' =>$req->input('password'),
                ];
                $response['token'] = auth()->attempt($credentials);
                return $this->sendResponse($response);
            }
        }catch(Exception $e){
            return ApiError($e->getCode(),$e->getMessage());
        }
    }
    # Login api
    public function login(Request $req){
        try{
            $rules = [
                'phone' => ['required'],
                'password' => ['required'],
                'token' => [],
            ];
            $validated = Validator::make($req->all(), $rules, $messages = [
                'phone.required' =>  __('lang.phone.required'),
                'password.required' => __('lang.password')
               
            ]);
            $validatedBody =  $this->validateBody($validated,$req->input());
            if($validated->fails()){
                return $validatedBody;
            }else{
                $credentials=[
                    'password'=>$req->input('password'),
                    'phone'=>$req->input('phone'),
                ];
                if(! $token = auth()->attempt($credentials)){
                    return  ApiError(401,__('lang.Unauthorized'));
                   
                }else{ 
                    if($req->input('token')){
                        $user = auth()->user();
                        $tokens = $user->token;
                        if(!in_array ($req->input('token'), $tokens)){
                            array_push($tokens, $req->input('token'));
                            auth()->user()->update(['token'=>$tokens]);
                        }
                    }
                    return response()->json([
                        'success' => true,
                        'user' =>auth()->user(),
                        'token' => $token,
                    ]);
                } 
            }
        }catch(Exception $e){
            return ApiError($e->getCode(),$e->getMessage());
        }
       
    }
    # Get Users
    public function getUsers(Request $req) {
        try{
            //return return ApiError(400,"");
            $user = auth()->user();
            //check if user is admin
            if (!in_array($user->type, ['ADMIN'])) 
                return ApiError(403,__('lang.notAllow'));

            $limit = $req->query('limit')??20;
            $page = $req->query('page')??1;
            //paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null);
            $users = User::paginate($limit,['*'],'page',$page);
            $usersCount = User::count();
            $pageCount = ceil($usersCount / $limit);
            return ApiResponse($users,$pageCount);
        }catch(Exception $e){
            return ApiError($e->getCode(),$e->getMessage());
        }
        
    }
    # Log the user out (Invalidate the token).
    public function logout(Request $req){
        try{
            auth()->logout();
            if($req->input('token')){
                $user = auth()->user();
                $tokens = $user->token;
                if(in_array ($req->input('token'), $tokens)){
                    $tokens = array_diff($tokens,[$req->input('token')]);
                    auth()->user()->update(['token'=>$tokens]);
                }
            }
            return response()->json(['success'=>true]);
            
            
        }catch(Exception $e){
            return ApiError($e->getCode(),$e->getMessage());
        }
        
    }
    # Refresh a token.
    public function refresh(){
        try{
            return $this->createToken(auth()->refresh());
        }catch(Exception $e){
            return ApiError($e->getCode(),$e->getMessage());
        }
        
    }
    
    public function updateUser(Request $req,$id){
        try{
            $rules = [
                'name' => ['max:255'],
                'phone' => [Rule::unique('users')->ignore($id)],
                'email' => [Rule::unique('users')->ignore($id),'email'],
                'type' => [Rule::in(['USER', 'ADMIN'])],
                'img' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'interests'=>[]
            ];
            $validated = Validator::make($req->all(), $rules, $messages = [
                'type.in' => __('lang.type.in'),
                'phone.unique' => __('lang.phone.unique'),
                'email.unique' => __('lang.email.unique'),
                'img' =>__('lang.img'),
                'interests' => []
            ]);
            $validatedBody =  $this->validateBody($validated,$req->input());
            if($validated->fails()){
                return $validatedBody;
            }else{
                if($req->input('interests'))
                    $validatedBody['interests'] = json_decode($req->input('interests'));
                if($req->hasFile('img'))
                    $validatedBody['image'] = uploadFile($req->file('img'));
                User::where('id',$id)->update($validatedBody);
                $response['data'] =  User::where('id',$id)->first();
                return $this->sendResponse($response);
            }

        }catch(Exception $e){
            return ApiError($e->getCode(),$e->getMessage());
        }
        
    }
    public function getUserById($id){
        try{
            $user = checkExistThenGet($id,User::class);
            return response()->json(['success'=>true, 'data'=>$user], 200);
        }catch(Exception $e){
            return ApiError($e->getCode(),$e->getMessage());
        }
        
    }
    public function deleteUser($id){
        try{
            $user = auth()->user();
            if (!in_array($user->type, ['ADMIN'])) 
                return ApiError(403,__('lang.notAllow'));
            checkExistThenGet($id,User::class);
            $data = User::find($id)->delete();
            return response()->json(['success' => 'true'], 200);
            
        }catch(Exception $e){
            return ApiError($e->getCode(),$e->getMessage());
        }
        
    }
    # send Mail
    public function sendMail(Request $req){
        try{
            $details = [
                'title' => 'Mail from Our App',
                'body' => 'Test mail sent by Laravel 8 using SMTP.'
            ];
           
            Mail::to($req->input('email'))->send(new sendMail($details));
            return response()->json(['success'=>true]);
        }catch(Exception $e){
            return ApiError($e->getCode(),$e->getMessage());
        }

    }
    public function upload(Request $req){
        try{
            error_log( App::currentLocale());
            $url = uploadFiles($req->file('img'));
            return response()->json(['msg' => 'success','url' =>$url], 200);
        }catch(Exception $e){
            return ApiError($e->getCode(),$e->getMessage());
        }
        
    }
     #Get the token array structure.
    protected function createToken($token){
        try{
            return response()->json([
                'success' => true,
                'user' =>auth()->user(),
                'token' => $token,
            ]);
        }catch(Exception $e){
            return ApiError($e->getCode(),$e->getMessage());
        }
        
    }
    #notifications
    public function getToken(){
        return view('welcome');
    }
    public function storeToken(Request $req){
        //auth()->user()->update(['token'=>$request->token]);
        error_log($req->token);
        return response()->json(['success' => 'true'], 200);
    }
    public function sendNotif(Request $req){
        $userId = $req->userId;
        sendNotification([
            "targetUser" => $userId, 
            "fromUser" => 2, 
            "text" => 'Test Api',
            "subject" => '4',
            "subjectType" => 'Welcom in my app',
            "info" =>'user'
        ]);
        return response()->json(['success' => 'true'], 200);

     }
    #get the notification
    public function getNotifs(Request $req){
        try{
            $user = auth()->user();
            $limit = $req->query('limit')??20;
            $page = $req->query('page')??1;
            $notifs = Notif::paginate($limit,['*'],'page',$page);
            $notifsCount = Notif::count();
            $pageCount = ceil($notifsCount / $limit);
            return ApiResponse($notifs,$pageCount);
        }catch(Exception $e){
            return ApiError($e->getCode(),$e->getMessage());
        }

    }
    #forget Password
    public function forgetPassword(Request $req){
        try{
            error_log("hhh");
            $rules = [
                'email' => ['required'],
            ];
            $validated = Validator::make($req->all(), $rules, $messages = [
                'email.required' =>  __('lang.email.required')
            ]);
            $validatedBody =  $this->validateBody($validated,$req->input());
            if($validated->fails()){
                return $validatedBody;
            }else{
                error_log("hhh");
                $user = checkEmailExist($req->input('email'),User::class);
                $verifyCode = random_int(1000, 9999);
                $user->verifyCode = $verifyCode;
                $user->save();

                $details = [
                    'title' => 'AlgoBlog Verify Code',
                    'body' => 'your verify code is : '.$verifyCode
                ];
                Mail::to($req->input('email'))->send(new sendMail($details));
                return response()->json(['success'=>true]);
            }
        }catch(Exception $e){
            return ApiError($e->getCode(),$e->getMessage());
        }

    }
    #verify code
    public function verifyCode(Request $req){
        try{
            $rules = [
                'email' => ['required'],
                'verifyCode' => ['required']
            ];
            $validated = Validator::make($req->all(), $rules, $messages = [
                'email.required' =>  __('lang.email.required'),
                'verifyCode.required' =>  __('lang.verifyCode.required')
            ]);
            $validatedBody =  $this->validateBody($validated,$req->input());
            if($validated->fails()){
                return $validatedBody;
            }else{
                $user = checkEmailExist($req->input('email'),User::class);
                if($user->verifyCode != $req->input('verifyCode')){
                    return ApiError(500,__('lang.verifyCode.notMatch'));
                }
                return response()->json(['success'=>true]);
            }
        }catch(Exception $e){
            return ApiError($e->getCode(),$e->getMessage());
        }

    }
    #verify code
    public function restPassword(Request $req){
        try{
            $rules = [
                'email' => ['required'],
                'password' => ['required']
            ];
            $validated = Validator::make($req->all(), $rules, $messages = [
                'email.required' =>  __('lang.email.required'),
                'password.required' =>  __('lang.password.required')
            ]);
            $validatedBody =  $this->validateBody($validated,$req->input());
            if($validated->fails()){
                return $validatedBody;
            }else{
                $user = checkEmailExist($req->input('email'),User::class);
                $user->password=$req->input('password');
                $user->save();
                return response()->json(['success'=>true]);
            }
        }catch(Exception $e){
            return ApiError($e->getCode(),$e->getMessage());
        }

    }
}

