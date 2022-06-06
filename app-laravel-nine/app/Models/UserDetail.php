<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    protected $table = 'user_detail';

    protected $fillable = [
        'identity_number',
        'first_name',
        'last_name',
        'gender',
        'birth_date',
        'birth_place',
        'religion',
        'phone_number',
        'full_address',
        'zip_code'
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
