<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Barangay extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    
    // Specify which fields are mass assignable
    protected $fillable = [
        'rhu_id',
        'name',
        'username',
        'email',
        'password',
        'email_verified_at',
    ];
}
