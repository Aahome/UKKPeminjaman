<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;


class ActivityLogController extends Controller
{
    public function index()
    {
        $logs = ActivityLog::with('user')
            ->latest()
            ->paginate(10);

        return view('admin.logs.index', compact('logs'));
    }
}
