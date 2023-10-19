<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Postcard extends Model
{
    use HasFactory;

    protected $fillable = [
        'uniq_id',
        'code',
        'guest_id',
        'file_carbon_path',
        'metric_tons',
    ];

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }
}
