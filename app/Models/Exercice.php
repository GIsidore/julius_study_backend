<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exercice extends Model
{
    protected $table = 'exercices';

    protected $fillable = ['lecon_id', 'type', 'enonce', 'donnees', 'difficulte', 'points', 'temps_estime', 'indice', 'tags'];

    protected $casts = [
        'donnees' => 'array',
        'tags' => 'array',
    ];

    public function lecon()
    {
        return $this->belongsTo(Lecon::class);
    }

    public function correction()
    {
        return $this->hasOne(Correction::class);
    }
}
