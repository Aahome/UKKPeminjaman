<?php

namespace App\Observers;

use App\Models\Tool;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ToolObserver
{
    public function created(Tool $tool)
    {
        $this->logActivity('created', $tool);
    }

    public function updated(Tool $tool)
    {
        $this->logActivity('updated', $tool);
    }

    public function deleted(Tool $tool)
    {
        $this->logActivity('deleted', $tool);
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
