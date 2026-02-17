<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


//mengimpor kelas dari namespace tertentu
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StaffUserController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\BorrowerController;
use App\Http\Controllers\BorrowController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Home
|--------------------------------------------------------------------------
*/

// Route halaman utama
Route::get('/', [HomeController::class, 'homeIndex'])->name('home');

/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/

// Route login hanya untuk guest (belum login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Route logout hanya untuk user yang sudah login
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Dashboard Redirect
|--------------------------------------------------------------------------
*/

// Redirect dashboard berdasarkan role user
Route::get('/dashboard', function () {

    // Jika belum login, arahkan ke login
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();

    // Jika user tidak memiliki role, logout untuk mencegah error
    if (!$user->role) {
        Auth::logout();
        return redirect()->route('login')
            ->withErrors(['email' => 'Account has no role assigned']);
    }

    // Redirect sesuai role menggunakan match expression
    return match ($user->role->role_name) {
        'admin'    => redirect()->route('admin.dashboard'),
        'staff'    => redirect()->route('staff.dashboard'),
        'borrower' => redirect()->route('borrower.dashboard'),
        default    => redirect()->route('login'),
    };
})->middleware('auth');

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

// Group route khusus admin (harus login & role admin)
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard admin
        Route::get('/dashboard', [HomeController::class, 'adminDashboard'])
            ->name('dashboard');

        // Resource Users (tanpa show, create, edit)
        Route::resource('users', UserController::class)->except('show', 'create', 'edit');

        // Resource Roles
        Route::resource('roles', RoleController::class)->except('show', 'create', 'edit');

        // Resource Tools
        Route::resource('tools', ToolController::class)->except('show', 'create', 'edit');

        // Resource Categories
        Route::resource('categories', CategoryController::class)->except('show', 'create', 'edit');

        // Menampilkan data peminjaman
        Route::get('/borrowings', [BorrowingController::class, 'index'])
            ->name('borrowings.index');

        // Proses peminjaman
        Route::resource('borrowings/borrow', BorrowController::class)
            ->except('show', 'index', 'create', 'edit');

        // Proses pengembalian
        Route::resource('borrowings/return', ReturnController::class)
            ->except('show', 'index', 'create', 'edit');

        // Menampilkan activity logs
        Route::get('activity_logs', [ActivityLogController::class, 'index'])
            ->name('logs.index');
    });

/*
|--------------------------------------------------------------------------
| STAFF
|--------------------------------------------------------------------------
*/

// Group route khusus staff
Route::middleware(['auth', 'role:staff'])
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {

        // Dashboard staff
        Route::get('/dashboard', [HomeController::class, 'staffDashboard'])
            ->name('dashboard');

        // Resource Users (Borrower Management)
        Route::resource('users', StaffUserController::class)->except('show', 'create', 'edit');

        // Menampilkan daftar peminjaman
        Route::get('/borrowings', [BorrowController::class, 'index'])
            ->name('borrowings.index');

        // Approve peminjaman
        Route::put('/borrowings/{borrowing}/approve',
            [BorrowController::class, 'approve']
        )->name('borrowings.approve');

        // Reject peminjaman
        Route::put('/borrowings/{borrowing}/reject',
            [BorrowController::class, 'reject']
        )->name('borrowings.reject');

        // Resource pengembalian (tanpa show, create, edit, store)
        Route::resource('/returns', ReturnController::class)
            ->except('show', 'create', 'edit', 'store');

        // Store data pengembalian
        Route::post('/returns/{borrowing}', [ReturnController::class, 'store'])
            ->name('returns.store');

        // Group laporan
        Route::prefix('reports')->name('reports.')->group(function () {

            // Laporan peminjaman
            Route::get('/borrowings', [ReportController::class, 'borrowings'])
                ->name('borrowings');

            // Laporan pengembalian
            Route::get('/returns', [ReportController::class, 'returns'])
                ->name('returns');

            // Laporan keseluruhan
            Route::get('/all', [ReportController::class, 'all'])
                ->name('all');
        });
    });

/*
|--------------------------------------------------------------------------
| BORROWER
|--------------------------------------------------------------------------
*/

// Group route khusus borrower
Route::middleware(['auth', 'role:borrower'])
    ->prefix('borrower')
    ->name('borrower.')
    ->group(function () {

        // Dashboard borrower
        Route::get('/dashboard', fn() => view('borrower.dashboard'))
            ->name('dashboard');

        // Menampilkan tools yang tersedia
        Route::get('/tools', [BorrowerController::class, 'AVIndex'])
            ->name('tools.index');

        // Simpan peminjaman
        Route::post('/borrowings/{tool}', [BorrowerController::class, 'store'])
            ->name('borrowings.store');

        // Proses pengembalian oleh borrower
        Route::post('/borrowings/{borrowing}/return',
            [BorrowerController::class, 'return']
        )->name('borrowings.return');

        // Resource borrowings (ID harus numeric)
        Route::resource('borrowings', BorrowerController::class)
            ->except(['create', 'store', 'show'])
            ->where(['borrowing' => '[0-9]+']);
    });
