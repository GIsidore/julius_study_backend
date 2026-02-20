<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Niveau extends Model
{
    protected $table = 'niveaux';

    protected $fillable = ['code', 'nom', 'ordre', 'cycle', 'description'];

    public $timestamps = false;

    public function chapitres()
    {
        return $this->hasMany(Chapitre::class);
    }
}
