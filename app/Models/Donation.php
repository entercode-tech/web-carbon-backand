<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'uniq_id',
        'order_id',
        'guest_id',
        'postcard_id',
        'amount',
        'currency',
        'status',
    ];

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function postcard()
    {
        return $this->belongsTo(Postcard::class);
    }
}
