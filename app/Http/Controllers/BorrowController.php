<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\ReturnModel;
use App\Models\Tool;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BorrowController extends Controller
{
    // Menampilkan seluruh data peminjaman untuk staff
    public function index()
    {
        $borrowings = Borrowing::with(['user', 'tool'])
            ->latest()
            ->get();

        return view('staff.borrowings.index', compact('borrowings'));
    }

    // Menyimpan data peminjaman baru oleh staff
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'           => 'required|exists:users,id',
            'tool_id'           => 'required|exists:tools,id',
            'quantity'          => 'required|integer',
            'borrow_date'       => 'nullable|date',
            'due_date'          => 'required|date|after_or_equal:borrow_date',
            'status'            => 'required|in:pending,approved,rejected,returned',
            'rejection_reason'  => 'nullable|string|max:255',
        ]);

        // Validasi tambahan untuk rejection_reason
        $validator->after(function ($validator) use ($request) {
            if ($request->status === 'rejected' && empty($request->rejection_reason)) {
                $validator->errors()->add(
                    'rejection_reason',
                    'Rejection reason is required when status is rejected.'
                );
            }

            if ($request->status !== 'rejected' && $request->rejection_reason) {
                $validator->errors()->add(
                    'rejection_reason',
                    'Rejection reason is only allowed when status is rejected.'
                );
            }
        });

        // Jika validasi gagal
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('open_create', true)
                ->with('form_context', 'create')
                ->with('error', 'Please check the form. Some fields are invalid.');
        }

        $tool = Tool::findOrFail($request->tool_id);

        // Cek stok jika status langsung approved
        if ($request->status === 'approved' && $request->quantity > $tool->stock) {
            return back()
                ->withErrors(['tool_id' => 'Tool stock is not available'])
                ->withInput()
                ->with('open_create', true)
                ->with('form_context', 'create')
                ->with('error', 'Tool stock is not available.');
        }


        // Membuat data borrowing
        $borrowing = Borrowing::create([
            'user_id'          => $request->user_id,
            'tool_id'          => $request->tool_id,
            'quantity'         => $request->quantity,
            'borrow_date'      => $request->borrow_date ?? now(),
            'due_date'         => $request->due_date,
            'status'           => $request->status,
            'rejection_reason' => $request->status === 'rejected' ? $request->rejection_reason : null,
        ]);

        // Jika status returned langsung, buat data return dan hitung denda
        if ($request->status === 'returned') {
            $today    = Carbon::today();
            $dueDate  = Carbon::parse($borrowing->due_date);

            // $lateDays = $today->gt($dueDate)
            //     ? $dueDate->diffInDays($today)
            //     : 0;

            // $fine = $lateDays * 5000 * $request->quantity;

            $fine = DB::selectOne("
            SELECT count_fine(?, ?, ?) AS total
            ", [$dueDate, $today, $request->quantity])->total;

            ReturnModel::create([
                'borrowing_id' => $borrowing->id,
                'return_date'  => $today,
                'fine'         => $fine,
            ]);
        }

        // Kurangi stok hanya jika status approved
        if ($request->status === 'approved') {
            $tool->decrement('stock', $request->quantity);
        }


        return redirect()
            ->route('admin.borrowings.index')
            ->with('success', 'Borrow request created successfully');
    }

    // Mengupdate data borrowing oleh staff
    public function update(Request $request, Borrowing $borrow)
    {
        $validator = Validator::make($request->all(), [
            'user_id'           => 'required|exists:users,id',
            'tool_id'           => 'required|exists:tools,id',
            'quantity'          => 'required|integer|min:1',
            'borrow_date'       => 'required|date',
            'due_date'          => 'required|date|after_or_equal:borrow_date',
            'status'            => 'required|in:pending,approved,rejected,returned',
            'rejection_reason'  => 'nullable|string|max:255',
        ]);

        // Validasi tambahan untuk rejection_reason
        $validator->after(function ($validator) use ($request) {
            if ($request->status === 'rejected' && empty($request->rejection_reason)) {
                $validator->errors()->add(
                    'rejection_reason',
                    'Rejection reason is required when status is rejected.'
                );
            }

            if ($request->status !== 'rejected' && $request->rejection_reason) {
                $validator->errors()->add(
                    'rejection_reason',
                    'Rejection reason is only allowed when status is rejected.'
                );
            }
        });

        // Jika validasi gagal
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('open_edit', true)
                ->with('form_context', 'edit')
                ->with('error', 'Please check the form. Some fields are invalid.');
        }


        $oldStatus = $borrow->status;
        $newStatus = $request->status;
        $qty       = $request->quantity;

        $oldTool = $borrow->tool;
        $newTool = Tool::findOrFail($request->tool_id);

        // Jika approved berubah menjadi returned, kembalikan stok
        if ($oldStatus === 'approved' && $newStatus === 'returned') {
            $oldTool->increment('stock', $qty);
        }

        // Jika approved berubah ke status lain selain approved/returned, kembalikan stok
        if ($oldStatus === 'approved' && !in_array($newStatus, ['approved', 'returned'])) {
            $oldTool->increment('stock', $qty);
        }

        // Tentukan apakah perlu pengurangan stok
        $shouldDecrement = (!in_array($oldStatus, ['approved', 'returned']) && $newStatus === 'approved') ||
            ($oldStatus === 'returned' && $newStatus === 'approved' && $borrow->returnData);

        // Jika perlu mengurangi stok
        if ($shouldDecrement) {
            if ($newTool->stock < $qty) {
                return back()
                    ->withErrors(['tool_id' => 'Tool stock is not available'])
                    ->withInput()
                    ->with('open_edit', true)
                    ->with('form_context', 'edit')
                    ->with('error', 'Tool stock is not available.');
            }

            $newTool->decrement('stock', $qty);
        }

        // Update data borrowing
        $borrow->update([
            'user_id'          => $request->user_id,
            'tool_id'          => $request->tool_id,
            'quantity'         => $qty,
            'borrow_date'      => $request->borrow_date,
            'due_date'         => $request->due_date,
            'status'           => $newStatus,
            'rejection_reason' => $newStatus === 'rejected'
                ? $request->rejection_reason
                : null,
        ]);

        // Jika masuk ke status returned, buat return data jika belum ada
        if (($oldStatus !== 'returned' && $newStatus === 'returned') || ($oldStatus == 'returned' && $newStatus === 'returned')) {
            if (!$borrow->returnData) {
                ReturnModel::create([
                    'borrowing_id' => $borrow->id,
                    'return_date'  => now(),
                    'fine'         => $request->fine ?? 0,
                ]);
            }
        }

        // Jika keluar dari status returned, hapus return data
        if ($oldStatus === 'returned' && $newStatus !== 'returned') {
            if ($borrow->returnData) {
                $borrow->returnData->delete();
            }
        }

        return redirect()
            ->route('admin.borrowings.index')
            ->with('success', 'Borrowing updated successfully');
    }

    // Menghapus data borrowing dan mengembalikan stok jika perlu
    public function destroy(Borrowing $borrow)
    {
        if ($borrow->status === 'approved') {
            $borrow->tool->increment('stock', $borrow->quantity);
        }

        $borrow->delete();

        return back()->with('success', 'Borrowing deleted');
    }

    // Menyetujui peminjaman dan mengurangi stok
    public function approve(Borrowing $borrowing)
    {
        if ($borrowing->status !== 'pending') {
            return back()->with('error', 'Only pending borrowings can be approved.');
        }


        if ($borrowing->tool->stock < $borrowing->quantity) {
            return back()
                ->withErrors(['stock' => 'Tool stock not available'])
                ->with('error', 'Tool stock not available.');
        }


        $borrowing->update([
            'status' => 'approved',
        ]);

        $borrowing->tool->decrement('stock', $borrowing->quantity);

        return back()->with('success', 'Borrowing approved');
    }

    // Menolak peminjaman dengan alasan
    public function reject(Request $request, Borrowing $borrowing)
    {
        if ($borrowing->status !== 'pending') {
            return back()
                ->withErrors(['status' => 'Only pending borrowings can be rejected.'])
                ->with('error', 'Only pending borrowings can be rejected.');
        }

        $request->validate([
            'rejection_reason' => 'nullable|string|max:255',
        ]);

        $borrowing->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Borrowing rejected successfully.');
    }
}
