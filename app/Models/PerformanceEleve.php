<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerformanceEleve extends Model
{
    protected $table = 'performance_eleves';

    protected $fillable = ['eleve_id', 'matiere_id', 'niveau_global', 'taux_reussite', 'points_forts', 'points_faibles'];

    protected $casts = [
        'niveau_global' => 'float',
        'taux_reussite' => 'float',
        'points_forts' => 'array',
        'points_faibles' => 'array',
    ];

    public function eleve()
    {
        return $this->belongsTo(Eleve::class);
    }

    public function matiere()
    {
        return $this->belongsTo(Matiere::class);
    }
}
