<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Correction extends Model
{
    protected $table = 'corrections';

    protected $fillable = ['exercice_id', 'reponse_correcte', 'explication', 'methode', 'erreurs_communes'];

    protected $casts = [
        'reponse_correcte' => 'array',
        'erreurs_communes' => 'array',
    ];

    public function exercice()
    {
        return $this->belongsTo(Exercice::class);
    }
}
