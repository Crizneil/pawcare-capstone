<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class ActivityLog extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'action', 'role', 'description', 'ip_address'];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper to get color based on role for your new column
    public function getRoleColorAttribute()
    {
        $role = strtolower($this->user->role ?? $this->role ?? '');

        return [
            'admin' => 'bg-danger-subtle text-danger',
            'staff' => 'bg-primary-subtle text-primary',
            'owner' => 'bg-success-subtle text-success',
        ][$role] ?? 'bg-secondary-subtle text-secondary';
    }

    public static function record($action, $description)
    {
        self::create([
            'user_id'   => auth()->id(),
            'action'    => $action,
            'role'      => auth()->user()->role ?? 'system',
            'description' => $description,
            'ip_address'   => request()->ip(),
    ]);
    }
}
