<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use App\Mail\SendOtpMail;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'id_google' => $googleUser->getId(),
                    'password' => Hash::make(Str::random(16))
                ]);
            } else {
                $user->update(['id_google' => $googleUser->getId()]);
            }

            $otp = strtoupper(Str::random(6));
 
            $user->update(['otp' => $otp]);

            Mail::to($user->email)->send(new SendOtpMail($otp));

            session(['temp_user_id' => $user->id]);

            return redirect()->route('otp.verify');

        } catch (\Exception $e) {
            dd($e->getMessage());
            return redirect('/login')->with('error', 'Gagal login dengan Google. Silakan coba lagi.');
        }
    }
}