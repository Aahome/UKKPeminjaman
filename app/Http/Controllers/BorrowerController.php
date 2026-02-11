<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Tool;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class BorrowerController extends Controller
{
    // Menampilkan daftar tools yang tersedia dengan fitur pencarian dan filter kategori
    public function AVIndex(Request $request)
    {
        $tools = Tool::with('category')
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('tool_name', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('category'), function ($q) use ($request) {
                $q->where('category_id', $request->category);
            })
            ->get();

        $categories = Category::all();

        return view('borrower.tools.index', compact('tools', 'categories'));
    }

    // Menampilkan daftar peminjaman milik user yang login
    public function index(Request $request)
    {
        $borrowings = Borrowing::with(['tool', 'returnData'])
            ->where('user_id', Auth::id())
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->whereHas('tool', function ($t) use ($request) {
                    $t->where('tool_name', 'like', '%' . $request->search . '%');
                });
            })
            ->latest()
            ->get();

        return view('borrower.borrowings.index', compact('borrowings'));
    }

    // Menyimpan data peminjaman baru
    public function store(Request $request, Tool $tool)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
            'due_date' => 'required|date|after_or_equal:today',
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('open_create', true)
                ->with('form_context', 'create');
        }

        // Cek ketersediaan stok
        if ($tool->stock < $request->quantity) {
            return back()
                ->with('error', 'Not enough stock.')
                ->withInput()
                ->with('open_create', true)
                ->with('form_context', 'create');
        }

        // Membuat data peminjaman
        Borrowing::create([
            'user_id'     => Auth::id(),
            'tool_id'     => $tool->id,
            'quantity'    => $request->quantity,
            'borrow_date' => Carbon::today(),
            'due_date'    => $request->due_date,
            'status'      => 'pending',
        ]);

        return redirect()
            ->route('borrower.borrowings.index')
            ->with('success', 'Borrowing request submitted.');
    }

    // Menampilkan detail peminjaman
    public function show(Borrowing $borrowing)
    {
        abort_if($borrowing->user_id !== Auth::user()->id, 403);

        return view('borrower.borrowings.show', compact('borrowing'));
    }

    // Mengupdate peminjaman jika status masih pending
    public function update(Request $request, Borrowing $borrowing)
    {
        if ($borrowing->status !== 'pending') {
            return back()->with('error', 'Only pending borrowings can be updated.');
        }

        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
            'due_date' => 'required|date|after_or_equal:today',
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('open_edit', true)
                ->with('form_context', 'edit')
                ->withInput(['borrow_id' => $borrowing->id]);
        }

        // Menghitung selisih jumlah untuk pengecekan stok
        $currentQty = $borrowing->quantity;
        $newQty     = $request->quantity;
        $diff       = $newQty - $currentQty;

        // Jika penambahan melebihi stok tersedia
        if ($diff > 0 && $borrowing->tool->stock < $diff) {
            return back()
                ->with('error', 'Not enough stock.')
                ->withInput()
                ->with('open_edit', true)
                ->with('form_context', 'edit')
                ->withInput(['borrow_id' => $borrowing->id]);
        }

        // Update data peminjaman
        $borrowing->update([
            'quantity' => $newQty,
            'due_date' => $request->due_date,
        ]);

        return redirect()
            ->route('borrower.borrowings.index')
            ->with('success', 'Borrowing updated successfully.');
    }

    // Mengubah status menjadi returned oleh borrower
    public function return(Borrowing $borrowing)
    {
        if ($borrowing->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Mencegah pengembalian dua kali
        if ($borrowing->status === 'returned') {
            return back()->with('error', 'This borrowing has already been returned.');
        }

        $borrowing->update([
            'status' => 'returned',
        ]);

        return back()->with('success', 'Tool returned successfully. Waiting for staff confirmation.');
    }

    // Menghapus peminjaman jika memenuhi syarat
    public function destroy(Borrowing $borrowing)
    {
        if ($borrowing->user_id !== Auth::user()->id) {
            abort(403);
        }

        // Hanya bisa dihapus jika pending atau approved
        if (!in_array($borrowing->status, ['pending', 'approved'])) {
            return back()->withErrors([
                'error' => 'You can only delete borrowing after the tool is returned or pending.'
            ]);
        }

        // Tidak bisa dihapus jika denda belum dibayar
        if ($borrowing->returnData && $borrowing->returnData->fine > 0) {
            return back()->withErrors([
                'error' => 'Borrowing cannot be deleted until the fine is paid.'
            ]);
        }

        $borrowing->delete();

        return back()->with('success', 'Borrowing deleted successfully.');
    }
}
