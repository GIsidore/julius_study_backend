<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lecon extends Model
{
    protected $table = 'lecons';

    protected $fillable = ['chapitre_id', 'titre', 'numero', 'contenu_html', 'contenu_json', 'resume', 'mots_cles', 'duree_lecture', 'difficulte'];

    protected $casts = [
        'contenu_json' => 'array',
        'media_urls' => 'array',
    ];

    public function chapitre()
    {
        return $this->belongsTo(Chapitre::class);
    }

    public function exercices()
    {
        return $this->hasMany(Exercice::class);
    }
}
