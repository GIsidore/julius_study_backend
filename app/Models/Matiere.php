<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Matiere extends Model
{
    protected $table = 'matieres';

    protected $fillable = ['nom', 'code', 'icone', 'couleur', 'description'];

    public $timestamps = false;

    public function chapitres()
    {
        return $this->hasMany(Chapitre::class);
    }
}
