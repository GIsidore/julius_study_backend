<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Eleve extends Model
{
    use HasApiTokens;

    protected $table = 'eleves';

    protected $fillable = ['nom', 'prenom', 'email', 'mot_de_passe', 'niveau_id', 'date_naissance', 'genre', 'telephone', 'ville', 'langue', 'photo_profil', 'derniere_connexion'];

    protected $hidden = ['mot_de_passe'];

    public function niveau()
    {
        return $this->belongsTo(Niveau::class);
    }
}
