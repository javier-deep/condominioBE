<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetCode extends Model
{
    protected $fillable = [
        'email',
        'code', 
        'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Verifica si el código ha expirado
     */
    public function hasExpired()
    {
        return $this->expires_at->isPast();
    }
}
