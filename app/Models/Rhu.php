<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Rhu extends Authenticatable
{
    use HasApiTokens,HasFactory, Notifiable;

    // Specify which fields are mass assignable
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
    ];
}
