<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
    public function index()
    {
        // Mengambil data activity log beserta relasi user, urut terbaru, dan paginasi 10 data per halaman
        $logs = ActivityLog::with('user')
            ->latest()
            ->paginate(10);

        // Mengirim data logs ke view admin.logs.index
        return view('admin.logs.index', compact('logs'));
    }
}
