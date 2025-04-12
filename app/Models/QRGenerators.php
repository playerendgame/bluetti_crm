<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QRGenerators extends Model
{
    use HasFactory;

    protected $table = 'qr_codes';

    protected $fillable = [
        'link',
        'qr_code_path'
    ];
}
