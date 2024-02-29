<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Auth;

class ProviderController extends Controller
{
    
    public function redirect($provider){

        return Socialite::driver($provider)->redirect();
        
       }
    
       public function callback($provider){
    
        $socialUser = Socialite::driver($provider)->user();
        dd($socialUser);
        $user = User::updateOrCreate([
            'github_id' => $socialUser->id,
        ], [
            'name' => $socialUser->name,
            'email' => $socialUser->email,
            'github_token' => $socialUser->token,
            'github_refresh_token' => $socialUser->refreshToken,
        ]);
     
        Auth::login($user);
     
        return redirect('/dashboard');
       }

}
