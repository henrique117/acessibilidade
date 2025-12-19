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

    /**
     * CORREÇÃO: Relacionamento Muitos-para-Muitos com Critérios.
     * Necessário para o @foreach ($itemChecklist->criterios ...) funcionar.
     */
    public function criterios()
    {
        // Tabela pivô: criterio_item
        return $this->belongsToMany(Criterio::class, 'criterio_item', 'item_id', 'criterio_id');
    }
}