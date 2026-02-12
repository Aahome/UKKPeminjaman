<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Halaman login
     */
    public function showLogin()
    {
        // Menampilkan view halaman login
        return view('auth.login');
    }

    /**
     * Proses login
     */
    public function login(Request $request)
    {
        // Validasi input email dan password dari form login
        $credentials = $request->validate([
            'name'    => 'required',
            'password' => 'required'
        ]);

        // Mencoba autentikasi user menggunakan data credentials
        if (Auth::attempt($credentials)) {
            // Regenerasi session untuk mencegah session fixation
            $request->session()->regenerate();

            // Redirect ke halaman yang sebelumnya dituju atau ke "/" jika tidak ada
            return redirect()->intended('/dashboard');
        }

        // Jika login gagal, kembali ke halaman sebelumnya dengan error
        return back()
            ->withErrors(['name' => 'Incorrect name or password'])
            ->onlyInput('name'); // Menyimpan kembali input email saja
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        // Menghapus autentikasi user yang sedang login
        Auth::logout();

        // Menghapus dan mengamankan session setelah logout
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect ke halaman utama setelah logout
        return redirect('/');
    }
}
