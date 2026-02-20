<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Niveau;
use App\Models\Matiere;
use App\Models\Chapitre;
use App\Models\Lecon;
use App\Models\Exercice;

class CoursController extends Controller
{
    public function getNiveaux()
    {
        return Niveau::orderBy('ordre')->get();
    }

    public function getMatieres()
    {
        return Matiere::all();
    }

    public function getChapitres()
    {
        return Chapitre::with(['niveau', 'matiere', 'lecons'])->get();
    }

    public function getLecon($id)
    {
        try {
            $lecon = Lecon::with(['chapitre.matiere', 'chapitre.niveau', 'exercices'])
                ->findOrFail($id);
            return response()->json($lecon);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Leçon non trouvée'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getExercicesLecon($leconId)
    {
        Lecon::findOrFail($leconId);

        return Exercice::where('lecon_id', $leconId)
            ->orderBy('difficulte')
            ->get();
    }
}
