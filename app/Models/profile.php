<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'age',
        'gender',
        'phone_number',
        'address',
        'birth_date',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
