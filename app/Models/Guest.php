<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = [
        'uniq_id',
        'first_name',
        'last_name',
        'location',
        'email',
    ];

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }
}
