<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chapitre extends Model
{
    protected $table = 'chapitres';

    protected $fillable = ['niveau_id', 'matiere_id', 'titre', 'numero', 'objectifs', 'prerequis', 'duree_estimee'];

    public function niveau()
    {
        return $this->belongsTo(Niveau::class);
    }

    public function matiere()
    {
        return $this->belongsTo(Matiere::class);
    }

    public function lecons()
    {
        return $this->hasMany(Lecon::class);
    }
}
