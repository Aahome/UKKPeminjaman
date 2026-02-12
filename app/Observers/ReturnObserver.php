<?php

namespace App\Observers;

use App\Models\ReturnModel;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ReturnObserver
{
    public function created(ReturnModel $return)
    {
        $this->logActivity('created', $return);
    }

    public function updated(ReturnModel $return)
    {
        $this->logActivity('updated', $return);
    }

    public function deleted(ReturnModel $return)
    {
        $this->logActivity('deleted', $return);
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
