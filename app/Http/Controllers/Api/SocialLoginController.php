<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Validation\Validator;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Auth;

class SocialLoginController extends Controller
{
    public function redirect($provider){
        return Socialite::driver($provider)->redirect();
       }
    
       public function callback($provider){

        $socialUser = Socialite::driver($provider)->user();

        $user = User::updateOrCreate([
            'provider'=>$provider,
            'provider_id' => $socialUser->id,
        ], [
            'name' => $socialUser->name,
            'nickname'=>User::generateUserName($socialUser->nickname),
            'email' => $socialUser->email,
            'provider_token' => $socialUser->token,
            
        ]);

        if($user){
            User::where('provider_id',$socialUser->id)->update([
            'email_verified_at'=>Carbon::now(),
            'created_at'=>Carbon::now(),
            ]);
        }
     
        Auth::login($user);
     
        return redirect('/dashboard');
       }
}
