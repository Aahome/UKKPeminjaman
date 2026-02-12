<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    // Menampilkan seluruh data role
    public function index()
    {
        return view('admin.roles.index', [
            'roles' => Role::all()
        ]);
    }

    // Menyimpan data role baru ke database
    public function store(Request $request)
    {
        // Validasi input role
        $validator = Validator::make($request->all(), [
            'role_name' => 'required|string|max:50|unique:roles,role_name',
        ]);

        // Jika validasi gagal, kembali dengan error dan buka modal create
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please check the form. Some fields are invalid.')
                ->with('open_create', true)
                ->with('form_context', 'create');
        }

        // Membuat role baru
        $role = Role::create([
            'role_name' => $request->role_name,
        ]);

        activity_log('role updated, Id:' . $role->id);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role created successfully');
    }

    // Memperbarui data role
    public function update(Request $request, Role $role)
    {
        // Validasi input (unik kecuali untuk role yang sedang diedit)
        $validator = Validator::make($request->all(), [
            'role_name' => 'required|string|max:50|unique:roles,role_name,' . $role->id,
        ]);

        // Jika validasi gagal, kembali dengan error dan buka modal edit
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please check the form. Some fields are invalid.')
                ->with('open_edit', true)
                ->with('form_context', 'edit');
        }

        // Update nama role
        $role->update([
            'role_name' => $request->role_name,
        ]);

        activity_log('role updated, Id:' . $role->id);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role updated successfully');
    }

    // Menghapus role jika tidak sedang digunakan oleh user
    public function destroy(Role $role)
    {

        // Hapus role
        $role->delete();
        
        activity_log('role deleted, Id:' . $role->id);

        return back()->with('success', 'Role deleted');
    }
}
