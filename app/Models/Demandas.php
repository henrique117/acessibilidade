<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Demandas extends Model
{
    use HasFactory;

    protected $table = 'avaliacao';

    protected $fillable = [
        'nome',
        'descricao',
        'password',
        'status',
        'paginas',
        'testeUsuario',
        'guideliness',
    ];

    protected $casts = [
        'paginas' => 'array',
        'testeUsuario' => 'boolean',
        'guideliness' => 'boolean',
    ];
    
    public function erros(){
        return $this->hasMany(Erro::class);
    }

    public function setPasswordAttribute($value){
        $this->attributes['password'] = Hash::make($value);
    }
}
