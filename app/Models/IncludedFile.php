<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncludedFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'uniq_id',
        'name',
        'file_path',
    ];
}
