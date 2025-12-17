<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $table = 'itens';

    protected $fillable = [
        'checklist_id',
        'descricao',
    ];

    public function checklist()
    {
        return $this->belongsTo(Checklist::class);
    }
}