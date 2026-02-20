<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgressionLecon extends Model
{
    protected $table = 'progression_lecons';

    protected $fillable = ['eleve_id', 'lecon_id', 'statut', 'temps_passe'];

    public function eleve()
    {
        return $this->belongsTo(Eleve::class);
    }

    public function lecon()
    {
        return $this->belongsTo(Lecon::class);
    }
}
