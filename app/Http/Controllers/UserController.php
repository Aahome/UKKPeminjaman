<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Mengambil data user beserta relasi role, dengan fitur filter pencarian dan filter role
        $users = User::with('role')
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('role'), function ($q) use ($request) {
                $q->where('role_id', $request->role);
            })
            ->get();

        // Mengambil seluruh data role untuk kebutuhan filter
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        // Mengambil data role untuk ditampilkan pada form create
        $roles = Role::all();

        return view('admin.users.create', compact('roles'));
    }

    // Menyimpan data user baru ke database
    public function store(Request $request)
    {
        // Validasi input form
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role_id'  => 'required|exists:roles,id',
        ]);

        // Jika validasi gagal, kembalikan ke halaman sebelumnya dengan error dan buka kembali modal create
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('open_create', true)
                ->with('form_context', 'create');
        }

        // Menyimpan user baru dengan password yang telah di-hash
        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role_id'  => $request->role_id,
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
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
            'role_id'  => 'required|exists:roles,id',
        ]);

        // Jika validasi gagal, kembalikan dengan error dan buka kembali modal edit
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
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

        // Update data user
        $user->update($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // Menghapus data user berdasarkan model binding
        $user->delete();

        // Redirect kembali ke halaman sebelumnya dengan pesan sukses
        return back()->with('success', 'Tool deleted');
    }
}
