@extends('layouts.app')

@section('title', 'Login')

@section('auth-content')

<!-- Main container -->
<main class="min-h-screen flex items-center justify-center bg-slate-100 text-slate-700 px-4">

     <!-- Back link top-left -->
     <a href="{{ url('/') }}" aria-label="Go home"
         class="fixed top-6 left-6 text-slate-500 text-base px-2 py-1 hover:bg-slate-200 hover:rounded-md transition-colors duration-150 z-50">&lt; Back</a>

    <!-- Card login -->
    <div class="w-full max-w-lg bg-white rounded-2xl shadow-md p-8">

        <!-- Logo -->
        <div class="flex justify-center mb-2">
            <img src="{{ asset('images/logo-image.png') }}" alt="UKK Peminjaman Logo" class="h-24 w-24 object-contain">
        </div>

        <!-- App title -->
        <h1 class="text-2xl font-bold text-slate-800 text-center">
            UKK<span class="text-blue-600">Peminjaman</span>
        </h1>

        <!-- Subtitle -->
        <p class="text-sm text-slate-500 text-center mt-1">
            Login to continue
        </p>


        <!-- Login form -->
        <form method="POST" action="{{ route('login') }}" class="mt-3 space-y-4">
            @csrf <!-- CSRF protection -->

            <!-- Username or Email input -->
            <div>
                <label class="text-sm text-slate-600">Username or Email</label>
                <input type="text"
                    name="username_or_email"
                    value="{{ old('username_or_email') }}"
                    class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring focus:ring-blue-200 focus:border-blue-500 outline-none"
                    required>
            </div>

            <!-- Password input -->
            <div>
                <label class="text-sm text-slate-600">Password</label>
                <input type="password"
                    name="password"
                    class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring focus:ring-blue-200 focus:border-blue-500 outline-none"
                    required>
            </div>

            <!-- Error message (login failed) -->
            @error('username_or_email')
            <div id="login-error" class="rounded-lg bg-red-500 mt-2 p-2 items-center text-center justify-center">
                        <p class="text-sm text-white text-center ">
                            {{ $message }}
                        </p>
                    </div>

            <script>
                setTimeout(() => {
                    const error = document.getElementById('login-error');
                    if (error) {
                        error.style.display = 'none';
                    }
                }, 3000); // 3000 ms = 3 detik
            </script>
            @enderror


            <!-- Submit button -->
            <button type="submit"
                class="w-full py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Login
            </button>
        </form>

    </div>
    <!-- End card -->

</main>
<!-- End main -->

@endsection