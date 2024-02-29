<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Exception;

class UserController extends Controller
{
    public function createUser(Request $request){

        try{
            $validator=Validator::make($request->all(),[
                'name'=>'required | string |min:4|max:10',
                'email'=>'required | email:rfc,dns |unique:users',
                'password'=>'required | max:8'
            ]);

            if($validator->fails()){
                return response()->json([
                    'status'=>true,
                    'message'=>"Unsuccesful To Inser!",
                    'Error-Message'=>$validator->errors(), 
            ]);
            }
            
            $data = User::create([
                'name'=>$request['name'],
                'email'=>$request['email'],
                'password'=>bcrypt($request['password']),
            ]);
    
            if($data){
                User::where('id',$data->id)->update([
                    'created_at'=>carbon::now(),
                ]);
            }
    
            if($data){
                return response()->json([
                    'status'=>true,
                    'message'=>"User Create Successfully!",
                    'data'=>$data
                ],201);
            }
    
            return response()->json([
                'status'=>false,
                'message'=>'failed to insert data !',
            ],400);

        }catch(Exception $e){
            $err = array('status'=>false,'Message'=>'Check all things','Data'=>$e->getMessage());
            return response()->json($err,404);
        }

    }

    public function allUser(){
        try{
            $alluser = User::where('status',1)->latest('id')->get();

        return response()->json([
            'status'=>true,
            'message'=>"Total " . count($alluser) .' User here',
            'data'=>$alluser,
        ],201);
        }catch(Exception $e){
            $data = array('status'=>false,'Message'=>'Faild to Fetch Data','Data'=>$e->getMessage());
            return response()->json($data,404);
        }
    }

    public function viewUser($id){
       try{
        $view =User::where('status',1)->where('id',$id)->first();
            if($view){
                return response()->json([
                    'status'=>true,
                    'message'=>$view->name . ' User FIND',
                    'data'=>$view,
                ],201);
            }else{
            return response()->json([
                'status'=>true,
                'message'=>'Failed to find',
                'data'=>'Plz Indicate Data Id'
            ],400);
        }
       }catch(Exception $e){
          return response()->json([
            'status'=>false,
            'message'=>"Api Error",
            'data'=>$e->getMessage(),
          ],404);
       }
    }

    public function edit(Request $request , $id){

        $user = User::find($id);
        if(!$user){
            return response()->json([
                'status'=>true,
                'message'=>"Error MESSAGE",
                'Data'=>"Data is Not Foound",
            ],404);
        }

        $validate = Validator::make($request->all(),[
            'name'=>'required | string ',
            'email'=>'required | string | unique:users,email,'.$id,
        ]);

        if($validate->fails()){
            return response()->json([
                'status'=>true,
                'mesage'=>'Validation Error',
                'Error-message'=>$validate->errors(), 
            ]);
        }

        // $update=User::where('id',$id)->update([
        //     'name'=>$request['name'],
        //     'email'=>$request['email'],
        //     'updated_at'=>Carbon::now(),
        // ]);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        if($user){
             return response()->json([
                    'status'=>true,
                    'message'=>'no Update Succesfully',
                    'data'=>$user,
                ],201);
        }
    }

    public function softdel($id){
        $user = User::find($id);

        $user->status = 0;

        $user->save();
        if($user){
            return response()->json([
                   'status'=>true,
                   'message'=>'Data Moved to Recycle Bin',
                   'data'=>$user,
               ],201);
       }
    }
    public function restore($id){
        $user = User::find($id);

        $user->status = 1;
        $user->created_at = Carbon::now();

        $user->save();
        if($user){
            return response()->json([
                   'status'=>true,
                   'message'=>'Data Restore SuccessFully',
                   'data'=>$user,
               ],201);
       }
    }

    public function delete($id){
        $user = User::find($id);

        $user->delete();
        if($user){
            return response()->json([
                   'status'=>true,
                   'message'=>'Data permanent  Delete',
                   'data'=>$user,
               ],201);
       }
    }


    public function login(Request $request){

        $validate = Validator::make($request->all(),[
            'email'=>'required',
            'password'=>'required',
        ]);

        if($validate->fails()){
            return response()->json([
                'status'=>true,
                'message'=>'Unauthorized!',
                'data'=>$validate->errors(),
            ]);
        }

        $credential = $request->only('email','password');

        if(Auth::attempt($credential)){
            $user = Auth::user();

            // creating a token
            $token =$user->createToken('Myapp')->accessToken;
            return response()->json([
                'status'=>true,
                'message'=>'Login SuccessFully',
                'data'=>$token,
            ],201);
        }
        return response()->json([
            'status'=>false,
            'message'=>'can not login',
            'data'=>$credential,
        ]);
    }

    public function logout(){
        $user=Auth::user();
        $user->tokens->each(function($token, $key){
                $token->delete();
            });
        return response()->json([
        'status'=>true,
        "message"=>"Logout",
         ]);
    }

}
