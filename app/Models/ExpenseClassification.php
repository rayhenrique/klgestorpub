<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseClassification extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean',
    ];
}
