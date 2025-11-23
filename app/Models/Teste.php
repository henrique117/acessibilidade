<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teste extends Model
{
    protected $table = 'teste';
    use HasFactory;

    protected $fillable = [
        'titulo',
        'dispositivo',
        'avaliacao_id',
    ];
}
