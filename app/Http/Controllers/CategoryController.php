<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    // Menampilkan daftar kategori dengan fitur pencarian
    public function index(Request $request)
    {
        $categories = Category::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('category_name', 'like', '%' . $request->search . '%');
            })
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    // Menampilkan halaman form tambah kategori
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Menyimpan kategori baru ke database
     */
    public function store(Request $request)
    {
        // Validasi input kategori
        $validator = Validator::make($request->all(), [
            'category_name' => 'required|string|max:100|unique:categories,category_name',
            'description'   => 'nullable|string|max:100',
        ]);

        // Jika validasi gagal, kembali dengan error dan buka modal create
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('open_create', true)
                ->with('form_context', 'create');
        }

        // Membuat kategori baru
        Category::create([
            'category_name' => $request->category_name,
            'description'   => $request->description,
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category added successfully');
    }

    /**
     * Menampilkan halaman edit kategori
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Memperbarui data kategori
     */
    public function update(Request $request, Category $category)
    {
        // Validasi input (unik kecuali kategori yang sedang diedit)
        $validator = Validator::make($request->all(), [
            'category_name' => 'required|string|max:100|unique:categories,category_name,' . $category->id,
            'description'   => 'nullable|string|max:100',
        ]);

        // Jika validasi gagal, kembali dengan error dan buka modal edit
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('open_edit', true)
                ->with('form_context', 'edit');
        }

        // Update data kategori
        $category->update([
            'category_name' => $request->category_name,
            'description'   => $request->description,
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category updated successfully');
    }

    /**
     * Menghapus kategori jika tidak memiliki relasi dengan tools
     */
    public function destroy(Category $category)
    {
        // Cegah penghapusan jika kategori masih digunakan oleh alat
        if ($category->tools()->exists()) {
            return back()->withErrors([
                'error' => 'Category is used by tools and cannot be deleted'
            ]);
        }

        // Hapus kategori
        $category->delete();

        return back()->with('success', 'Category deleted');
    }
}
