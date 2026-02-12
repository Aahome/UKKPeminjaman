<?php

namespace App\Observers;

use App\Models\Borrowing;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class BorrowingObserver
{
    public function created(Borrowing $borrowing)
    {
        $this->logActivity('created', $borrowing);
    }

    public function updated(Borrowing $borrowing)
    {
        $this->logActivity('updated', $borrowing);
    }

    public function deleted(Borrowing $borrowing)
    {
        $this->logActivity('deleted', $borrowing);
    }

    protected function logActivity(string $action, $model): void
    {
        if (!Auth::check()) return; // skip if no user is logged in

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => strtoupper($action) . ' ' . strtolower(class_basename($model)) . ': ' . $model->id,
            'old_data' => $model->getOriginal() ? json_encode($model->getOriginal()) : null,
            'new_data' => json_encode($model->toArray()),
        ]);
    }
}
