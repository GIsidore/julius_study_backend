<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReponseEleve extends Model
{
    protected $table = 'reponse_eleves';

    protected $fillable = ['eleve_id', 'exercice_id', 'reponse', 'est_correcte', 'temps_reponse'];

    protected $casts = [
        'reponse' => 'array',
        'est_correcte' => 'boolean',
    ];

    public function eleve()
    {
        return $this->belongsTo(Eleve::class);
    }

    public function exercice()
    {
        return $this->belongsTo(Exercice::class, 'exercice_id', 'id');
    }
}
