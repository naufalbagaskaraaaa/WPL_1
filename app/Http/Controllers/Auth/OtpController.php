<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtpController extends Controller
{
    public function showVerifyForm()
    {
        //dd(session()->all());
        if (!session()->has('temp_user_id')) {
            return redirect('/login');
        }
        return view('auth.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6'
        ], [
            'otp.size' => 'Kode OTP harus tepat 6 karakter!',
            'otp.required' => 'Silakan masukkan kode OTP dengan benar'
        ]);

        $userId = session('temp_user_id');
        $user = User::find($userId);

        $inputOtp = trim(strtoupper($request->otp));

        if ($user && strtoupper($request->otp) === $user->otp) {
            $user->update(['otp' => null]);
            Auth::login($user);
            session()->forget('temp_user_id');
            
            return redirect('/dashboard');
        }

        return back()->with('error', 'Kode OTP salah atau sudah kedaluwarsa.');
    }
}