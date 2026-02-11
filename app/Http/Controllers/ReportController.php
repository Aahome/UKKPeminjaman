<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Generate laporan seluruh data peminjaman dalam format PDF
     */
    public function borrowings()
    {
        // Mengambil data peminjaman berdasarkan beberapa status
        $borrowings = Borrowing::with(['user', 'tool'])
            ->whereIn('status', ['pending', 'approved', 'rejected','returned'])
            ->orderBy('borrow_date')
            ->get();

        // Membuat file PDF dari view laporan peminjaman
        $pdf = Pdf::loadView('staff.reports.borrowings', [
            'borrowings' => $borrowings,
            'date' => Carbon::now()->format('d M Y'), // Tanggal cetak laporan
        ]);

        // Menampilkan preview PDF di browser
        return $pdf->stream('borrowing-report.pdf');
    }

    /**
     * Generate laporan data pengembalian dalam format PDF
     */
    public function returns()
    {
        // Mengambil data peminjaman yang sudah dikembalikan
        $borrowings = Borrowing::with(['user', 'tool', 'returnData'])
            ->where('status', 'returned')
            ->orderBy('due_date')
            ->get();

        // Membuat file PDF dari view laporan pengembalian
        $pdf = Pdf::loadView('staff.reports.returns', [
            'borrowings' => $borrowings,
            'date' => Carbon::now()->format('d M Y'),
        ]);

        // Menampilkan preview PDF di browser
        return $pdf->stream('return-report.pdf');
    }

    /**
     * Generate laporan lengkap (peminjaman + pengembalian) dalam format PDF
     */
    public function all()
    {
        // Mengambil seluruh data peminjaman beserta relasinya
        $borrowings = Borrowing::with(['user', 'tool', 'returnData'])
            ->orderBy('borrow_date')
            ->get();

        // Membuat file PDF dari view laporan lengkap
        $pdf = Pdf::loadView('staff.reports.all', [
            'borrowings' => $borrowings,
            'date' => Carbon::now()->format('d M Y'),
        ]);

        // Menampilkan preview PDF di browser
        return $pdf->stream('full-report.pdf');
    }
}
