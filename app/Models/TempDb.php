<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempDb extends Model
{
    use HasFactory;

    protected $table = 'tempdb';
    protected $guarded = [];
}
