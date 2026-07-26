<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $fillable = [
    'name',
    'email',
    'username',
    'avatar',
    'date_of_birth',
    'phone',
    'nationality',
    'ethnicity',
    'birth_place',
    'current_address',
    'gender',
    'password',
    'role',
    'login_token',
];

    protected $hidden = [
        'password',
        'remember_token',
        'login_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'date_of_birth'     => 'date',
        ];
    }

    // One user has one teacher profile
    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }

    // Role helpers
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    /**
 * Auto-generate username based on role and DOB
 */
public static function generateUsername(string $role, ?string $dob = null): string
{
    $prefix = match($role) {
        'admin'   => 'ADM',
        'teacher' => 'TCH',
        'student' => 'STU',
        default   => 'USR',
    };

    if ($dob) {
        $suffix = \Carbon\Carbon::parse($dob)->format('dmY');
    } else {
        $suffix = str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT);
    }

    $username = $prefix . $suffix;

    // Ensure uniqueness
    $original = $username;
    $counter = 1;
    while (self::where('username', $username)->exists()) {
        $username = $original . chr(64 + $counter);
        $counter++;
    }

    return $username;
}

/**
 * Get avatar URL
 */
public function avatarUrl(): ?string
{
    return $this->avatar 
        ? asset('storage/' . $this->avatar) 
        : null;
}

/**
 * Get initials for fallback avatar
 */
public function initials(): string
{
    return strtoupper(substr($this->name, 0, 1));
}

/**
 * Generate a new login token
 */
public function generateLoginToken(): string
{
    do {
        $token = bin2hex(random_bytes(32)); // 64 char hex
    } while (self::where('login_token', $token)->exists());

    $this->update(['login_token' => $token]);
    return $token;
}

/**
 * Get QR login URL
 */
public function qrLoginUrl(): string
{
    if (!$this->login_token) {
        $this->generateLoginToken();
        $this->refresh();
    }

    return route('login.qr', ['token' => $this->login_token]);
}

}
