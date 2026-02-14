<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Erro extends Model
{
    use HasFactory;

    protected $table = 'erros';

    protected $fillable = [
        'pgs',
        'id_item',
        'em_cfmd',
        'descricao',
        'criticidade',
        'comportamento_esperado',
        'avaliacao_id',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'id_item');
    }

    public function images()
    {
        return $this->hasMany(Image::class, 'id_erro');
    }
}