<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Mengambil data user beserta relasi role dan grade, dengan fitur filter pencarian dan filter role
        $users = User::with('role', 'grade')
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('role'), function ($q) use ($request) {
                $q->where('role_id', $request->role);
            })
            ->get();

        // Mengambil seluruh data role dan grade untuk kebutuhan filter
        $roles = Role::all();
        $gradesData = Grade::all();

        return view('admin.users.index', compact('users', 'roles', 'gradesData'));
    }

    // Menyimpan data user baru ke database
    public function store(Request $request)
    {
        // Validasi input form
        $validator = Validator::make($request->all(), [
            'name'       => 'required|string|max:255',
            'username'   => 'required|string|unique:users,username|max:255',
            'email'      => 'required|email|unique:users,email',
            'phone_number' => 'nullable|string|max:20',
            'password'   => 'required|min:6|confirmed',
            'role_id'    => 'required|exists:roles,id',
            'grade_id'   => 'nullable|exists:grades,id',
        ]);

        // Jika validasi gagal, kembalikan ke halaman sebelumnya dengan error dan buka kembali modal create
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please check the form. Some fields are invalid.')
                ->with('open_create', true)
                ->with('form_context', 'create');
        }

        // Menyimpan user baru dengan password yang telah di-hash
        $user = User::create([
            'name'         => $request->name,
            'username'     => $request->username,
            'email'        => $request->email,
            'phone_number' => $request->phone_number,
            'password'     => Hash::make($request->password),
            'role_id'      => $request->role_id,
            'grade_id'     => $request->grade_id,
            'created_by'   => Auth::id(),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    // Memperbarui data user yang sudah ada
    public function update(Request $request, User $user)
    {
        // Validasi input form (email unik kecuali untuk user yang sedang diedit)
        $validator = Validator::make($request->all(), [
            'name'         => 'required|string|max:255',
            'username'     => 'required|string|max:255|unique:users,username,' . $user->id,
            'email'        => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
            'password'     => 'nullable|min:6|confirmed',
            'role_id'      => 'required|exists:roles,id',
            'grade_id'     => 'nullable|exists:grades,id',
        ]);

        // Jika validasi gagal, kembalikan dengan error dan buka kembali modal edit
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please check the form. Some fields are invalid.')
                ->with('open_edit', true)
                ->with('form_context', 'edit');
        }

        $validated = $validator->validated();

        // Hash password hanya jika field password diisi
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        if ($user->role->role_name === 'admin' && $request->role_id != $user->role_id) {
            return back()->with('error', 'Admin role cannot be changed.');
        }

        $validated['modified_by'] = Auth::id();
        $validated['username'] = $request->username;
        $validated['phone_number'] = $request->phone_number;
        $validated['grade_id'] = $request->grade_id;

        // Update data user
        $user->update($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // Check if user has approved borrowings and increment tool stock
        $approvedBorrowings = $user->borrowings()->where('status', 'approved')->get();
        
        foreach ($approvedBorrowings as $borrowing) {
            $borrowing->tool->increment('stock', $borrowing->quantity);
        }

        // Menghapus data user berdasarkan model binding
        $user->delete();

        // Redirect kembali ke halaman sebelumnya dengan pesan sukses
        return back()->with('success', 'user deleted');
    }
}
