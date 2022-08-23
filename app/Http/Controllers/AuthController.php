<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ThrottlesLogins;

class AuthController extends Controller
{
    use ThrottlesLogins;

    public function formLogin(Request $request)
    {
        return view('auth.login');
    }

    public function prosesLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($this->hasTooManyLoginAttempts($request)) {
            return back()->with(['type' => 'danger', 'message' => 'terlalu banyak melakukan percobaan login, silakan tunggu beberapa waktu untuk login kemabali!']);
        }

        if (auth()->attempt($request->only('email', 'password'), $request->filled('remember'))) {
            return redirect('/beranda');
        }

        $this->incrementLoginAttempts($request);
        return back()->with(['type' => 'danger', 'message' => 'email atau password salah!']);
    }

    public function prosesLogout(Request $request)
    {
        auth()->logout();
        return redirect('/');
    }
}
