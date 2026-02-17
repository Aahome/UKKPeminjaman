<?php

namespace App\Http\Controllers;

use App\Models\Tool;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ToolController extends Controller
{
    /**
     * Menampilkan daftar alat (admin) dengan fitur pencarian dan filter kategori
     */
    public function index(Request $request, Tool $tool)
    {
        $tools = Tool::with('category')
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('tool_name', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('category'), function ($q) use ($request) {
                $q->where('category_id', $request->category);
            })
            ->paginate(10);

        // Mengambil seluruh kategori untuk kebutuhan filter
        $categories = Category::all();

        return view('admin.tools.index', compact('tools', 'categories'));
    }

    /**
     * Menyimpan data alat baru ke database
     */
    public function store(Request $request)
    {
        // Validasi input alat
        $validator = Validator::make($request->all(), [
            'tool_name'   => 'required|string|max:100|unique:tools,tool_name',
            'category_id' => 'required|exists:categories,id',
            'condition'   => 'required|in:good,damaged',
            'stock'       => 'required|integer|min:0',
            'price'       => 'required|numeric|min:0',
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

        // Membuat data alat baru
        $tool = Tool::create($request->only([
            'tool_name',
            'category_id',
            'condition',
            'stock',
            'price'
        ]) + ['created_by' => Auth::id()]);

        return redirect()
            ->route('admin.tools.index')
            ->with('success', 'Tool added successfully');
    }

    /**
     * Memperbarui data alat
     */
    public function update(Request $request, Tool $tool)
    {
        // Validasi input (unik kecuali alat yang sedang diedit)
        $validator = Validator::make($request->all(), [
            'tool_name'   => 'required|string|max:100|unique:tools,tool_name,' . $tool->id,
            'category_id' => 'required|exists:categories,id',
            'condition'   => 'required|in:good,damaged',
            'stock'       => 'required|integer|min:0',
            'price'       => 'required|numeric|min:0',
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

        // Update data alat
        $tool->update($request->only([
            'tool_name',
            'category_id',
            'condition',
            'stock',
            'price'
        ]) + ['modified_by' => Auth::id()]);

        return redirect()
            ->route('admin.tools.index')
            ->with('success', 'Tool updated successfully');
    }

    /**
     * Menghapus data alat
     */
    public function destroy(Tool $tool)
    {
        $tool->delete();

        return back()->with('success', 'Tool deleted');
    }
}
