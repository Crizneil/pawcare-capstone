<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRequest extends Model
{
    // This allows the Controller to read and write these columns
    protected $fillable = ['user_id', 'type', 'message', 'status'];

    // This connects the request to a specific user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
