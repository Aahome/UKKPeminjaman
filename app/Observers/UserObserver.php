<?php

namespace App\Observers;

use App\Models\User;          // Import model User
use App\Models\ActivityLog;   // Import model ActivityLog untuk menyimpan log
use Illuminate\Support\Facades\Auth; // Digunakan untuk mengambil user yang sedang login

class UserObserver
{
    /**
     * Method ini otomatis dipanggil saat data User dibuat (INSERT)
     */
    public function created(User $user)
    {
        $this->logActivity('created', $user);
    }

    /**
     * Method ini otomatis dipanggil saat data User diperbarui (UPDATE)
     */
    public function updated(User $user)
    {
        $this->logActivity('updated', $user);
    }

    /**
     * Method ini otomatis dipanggil saat data User dihapus (DELETE)
     */
    public function deleted(User $user)
    {
        $this->logActivity('deleted', $user);
    }

    /**
     * Function utama untuk menyimpan log aktivitas
     * 
     * @param string $action  -> jenis aksi (created, updated, deleted)
     * @param User   $user    -> data user yang sedang diproses
     */
    protected function logActivity(string $action, User $user): void
    {
        // Jika tidak ada user yang login, hentikan proses logging
        // (untuk menghindari error user_id null)
        if (!Auth::check()) return;

        ActivityLog::create([
            // ID user yang sedang login (pelaku aksi)
            'user_id' => Auth::id(),

            // Deskripsi aktivitas (contoh: CREATED user: 5)
            'activity' => strtoupper($action) . ' user: ' . $user->id,

            // Data sebelum perubahan (untuk UPDATE & DELETE)
            // Akan bernilai null jika tidak ada perubahan sebelumnya
            'old_data' => $user->getOriginal() 
                ? json_encode($user->getOriginal()) 
                : null,

            // Data setelah perubahan (untuk CREATE & UPDATE)
            'new_data' => json_encode($user->toArray()),
        ]);
    }
}
