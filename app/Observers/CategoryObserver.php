<?php

namespace App\Observers;

use App\Models\Category;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class CategoryObserver
{
    public function created(Category $category)
    {
        $this->logActivity('created', $category);
    }

    public function updated(Category $category)
    {
        $this->logActivity('updated', $category);
    }

    public function deleted(Category $category)
    {
        $this->logActivity('deleted', $category);
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
