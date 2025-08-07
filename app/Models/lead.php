<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'source',
        'status',
        'potential_value',
        'notes',
        'contact_id',
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
