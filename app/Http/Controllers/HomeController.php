<?php

namespace App\Http\Controllers;

use App\Models\User;

class HomeController extends Controller
{
    // Menampilkan halaman utama (home) beserta seluruh data user
    public function homeIndex()
    {
        return view('home', [
            'users' => User::all()
        ]);
    }

    // Menampilkan halaman dashboard untuk admin
    public function adminDashboard()
    {
        return view('admin.dashboard');
    }

    // Menampilkan halaman dashboard untuk staff
    public function staffDashboard()
    {
        return view('staff.dashboard');
    }
}
