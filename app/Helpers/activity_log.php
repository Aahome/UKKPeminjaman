<?php

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

if (!function_exists('activity_log')) {
    function activity_log(string $activity): void
    {
        // Hentikan jika user belum login
        if (!Auth::check()) {
            return;
        }

        // Simpan aktivitas user ke tabel activity_logs
        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => $activity,
        ]);
    }
}
