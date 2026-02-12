<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\ReturnModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ReturnController extends Controller
{
    /**
     * Menampilkan daftar peminjaman yang sudah disetujui atau dikembalikan
     */
    public function index()
    {
        $borrowings = Borrowing::with(['user', 'tool','returnData'])
            ->whereIn('status', ['approved', 'returned'])
            ->latest()
            ->get();

        return view('staff.returns.index', compact('borrowings'));
    }

    /**
     * Menyimpan data pengembalian dan menghitung denda otomatis
     */
    public function store(Borrowing $borrowing)
    {
        $today   = Carbon::today(); // Tanggal hari ini
        $dueDate = Carbon::parse($borrowing->due_date); // Tanggal jatuh tempo

        // Menghitung jumlah hari keterlambatan
        $lateDays = $today->greaterThan($dueDate)
            ? $today->diffInDays($dueDate)
            : 0;

        // Menghitung total denda (Rp5000 × hari terlambat × jumlah alat)
        $fine = $lateDays * 5000 * $borrowing->quantity;

        // Menyimpan data pengembalian
        ReturnModel::create([
            'borrowing_id' => $borrowing->id,
            'return_date'  => $today,
            'fine'         => $fine,
        ]);

        // Mengembalikan stok alat
        $borrowing->tool->increment('stock', $borrowing->quantity);

        return back()
            ->with('view', 'return')
            ->with('success', 'Tool returned successfully');
    }

    /**
     * Memperbarui data pengembalian dan menghitung ulang denda
     */
    public function update(Request $request, ReturnModel $return)
    {
        // Validasi tanggal pengembalian
        $validator = Validator::make($request->all(), [
            'return_date' => 'required|date|after_or_equal:' . $return->borrowing->borrow_date,
        ]);

        // Jika validasi gagal, kembali dengan error dan buka modal edit
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please check the form. Some fields are invalid.')
                ->with('view', 'return')
                ->with('open_edit', true)
                ->with('form_context', 'edit');
        }

        // Parsing tanggal
        $returnDate = Carbon::parse($request->return_date);
        $dueDate    = Carbon::parse($return->borrowing->due_date);

        // Menghitung ulang denda berdasarkan tanggal baru
        if ($returnDate->greaterThan($dueDate)) {
            $lateDays = $dueDate->diffInDays($returnDate);
            $fine = $lateDays * 5000 * $return->borrowing->quantity;
        } else {
            $fine = 0;
        }

        // Update data pengembalian
        $return->update([
            'return_date' => $returnDate,
            'fine'        => $fine,
        ]);

        return redirect()
            ->route('admin.borrowings.index')
            ->with('view', 'return')
            ->with('success', 'Return data updated successfully');
    }

    /**
     * Menghapus data pengembalian dan mengembalikan status peminjaman
     */
    public function destroy(ReturnModel $return)
    {
        // dd($borrowing = $return->borrowing, $return->id);
        $borrowing = $return->borrowing;

        // Mengubah status peminjaman kembali menjadi pending
        $borrowing->update([
            'status' => 'pending',
        ]);

        // Menghapus data pengembalian
        $return->delete();

        return back()
            ->with('view', 'return')
            ->with('success', 'Return data deleted and borrowing reverted.');
    }
}
