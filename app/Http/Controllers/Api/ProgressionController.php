<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProgressionController extends Controller
{
    public function index(Request $request)
    {
        $eleve = $request->user();
        
        // Progression par matière
        $progressionMatieres = DB::table('matieres')
            ->select(
                'matieres.id',
                'matieres.nom',
                'matieres.icone',
                'matieres.couleur',
                DB::raw('COUNT(DISTINCT chapitres.id) as total_chapitres'),
                DB::raw('COUNT(DISTINCT CASE WHEN progression_lecons.statut = "termine" THEN chapitres.id END) as chapitres_termines'),
                DB::raw('COUNT(DISTINCT lecons.id) as total_lecons'),
                DB::raw('COUNT(DISTINCT progression_lecons.lecon_id) as lecons_terminees'),
                DB::raw('COALESCE(AVG(CASE WHEN reponses_eleves.est_correcte THEN 100 ELSE 0 END), 0) as taux_reussite')
            )
            ->join('chapitres', 'chapitres.matiere_id', '=', 'matieres.id')
            ->join('lecons', 'lecons.chapitre_id', '=', 'chapitres.id')
            ->leftJoin('progression_lecons', function($join) use ($eleve) {
                $join->on('progression_lecons.lecon_id', '=', 'lecons.id')
                     ->where('progression_lecons.eleve_id', '=', $eleve->id);
            })
            ->leftJoin('exercices', 'exercices.lecon_id', '=', 'lecons.id')
            ->leftJoin('reponses_eleves', function($join) use ($eleve) {
                $join->on('reponses_eleves.exercice_id', '=', 'exercices.id')
                     ->where('reponses_eleves.eleve_id', '=', $eleve->id);
            })
            ->where('chapitres.niveau_id', $eleve->niveau_id)
            ->groupBy('matieres.id', 'matieres.nom', 'matieres.icone', 'matieres.couleur')
            ->get()
            ->map(function($matiere) {
                $progression = $matiere->total_lecons > 0 
                    ? round(($matiere->lecons_terminees / $matiere->total_lecons) * 100, 1)
                    : 0;
                
                return [
                    'matiere' => [
                        'id' => $matiere->id,
                        'nom' => $matiere->nom,
                        'icone' => $matiere->icone,
                        'couleur' => $matiere->couleur,
                    ],
                    'total_chapitres' => $matiere->total_chapitres,
                    'chapitres_termines' => $matiere->chapitres_termines,
                    'total_lecons' => $matiere->total_lecons,
                    'lecons_terminees' => $matiere->lecons_terminees,
                    'progression' => $progression,
                    'taux_reussite' => round($matiere->taux_reussite, 1),
                ];
            });
        
        // Statistiques globales
        $statsGlobales = [
            'total_lecons_disponibles' => DB::table('lecons')
                ->join('chapitres', 'chapitres.id', '=', 'lecons.chapitre_id')
                ->where('chapitres.niveau_id', $eleve->niveau_id)
                ->count(),
            'lecons_terminees' => DB::table('progression_lecons')
                ->where('eleve_id', $eleve->id)
                ->where('statut', 'termine')
                ->count(),
            'exercices_reussis' => DB::table('reponses_eleves')
                ->where('eleve_id', $eleve->id)
                ->where('est_correcte', true)
                ->count(),
            'points_totaux' => DB::table('reponses_eleves')
                ->join('exercices', 'exercices.id', '=', 'reponses_eleves.exercice_id')
                ->where('reponses_eleves.eleve_id', $eleve->id)
                ->where('reponses_eleves.est_correcte', true)
                ->sum('exercices.points'),
        ];
        
        $statsGlobales['progression_globale'] = $statsGlobales['total_lecons_disponibles'] > 0
            ? round(($statsGlobales['lecons_terminees'] / $statsGlobales['total_lecons_disponibles']) * 100, 1)
            : 0;
        
        // Badges
        $badges = $this->calculerBadges($eleve, $statsGlobales);
        
        return response()->json([
            'progression_matieres' => $progressionMatieres,
            'stats_globales' => $statsGlobales,
            'badges' => $badges,
        ]);
    }
    
    private function calculerBadges($eleve, $stats)
    {
        $badges = [];
        
        // Premier pas
        if ($stats['lecons_terminees'] >= 1) {
            $badges[] = [
                'id' => 'premier_pas',
                'nom' => 'Premier pas',
                'icone' => '🎯',
                'description' => 'Première leçon terminée',
                'debloque' => true,
            ];
        }
        
        // Marathon
        if ($stats['lecons_terminees'] >= 5) {
            $badges[] = [
                'id' => 'marathon',
                'nom' => 'Marathon',
                'icone' => '🏃',
                'description' => '5 leçons terminées',
                'debloque' => true,
            ];
        }
        
        // Expert
        $badges[] = [
            'id' => 'expert',
            'nom' => 'Expert',
            'icone' => '🏅',
            'description' => '100 exercices réussis',
            'debloque' => $stats['exercices_reussis'] >= 100,
            'progression' => min(100, ($stats['exercices_reussis'] / 100) * 100),
        ];
        
        return $badges;
    }
}
