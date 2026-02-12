<?php

namespace App\Observers;

use App\Models\Role;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class RoleObserver
{
    public function created(Role $role)
    {
        $this->logActivity('created', $role);
    }

    public function updated(Role $role)
    {
        $this->logActivity('updated', $role);
    }

    public function deleted(Role $role)
    {
        $this->logActivity('deleted', $role);
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
