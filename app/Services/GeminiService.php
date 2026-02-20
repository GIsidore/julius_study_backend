<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private $apiKey;

    private $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
    }

    public function genererReponse(string $prompt, int $maxTokens = 500): string
    {
        try {
            $url = "https://generativelanguage.googleapis.com/v1/models/gemini-2.5-flash:generateContent?key={$this->apiKey}";

            $response = Http::timeout(30)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'topK' => 40,
                        'topP' => 0.95,
                        'maxOutputTokens' => $maxTokens,
                    ],
                ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    return $data['candidates'][0]['content']['parts'][0]['text'];
                }

                Log::error('Format réponse Gemini invalide', ['data' => $data]);
                return 'Désolé, je n\'ai pas pu comprendre la réponse.';
            }

            Log::error('Erreur API Gemini', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return 'Erreur de connexion à l\'IA. Vérifiez votre clé API.';
        } catch (\Exception $e) {
            Log::error('Exception Gemini', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return 'Erreur technique : ' . $e->getMessage();
        }
    }

    public function analyserErreur(string $question, string $reponseEleve, string $reponseCorrecte, string $niveau): string
    {
        $prompt = "Tu es un tuteur bienveillant pour un élève de {$niveau}.

Question : {$question}
Réponse de l'élève : {$reponseEleve}
Bonne réponse : {$reponseCorrecte}

Génère un feedback qui :
1. Encourage l'élève (ne jamais décourager)
2. Explique l'erreur de façon simple
3. Donne UN indice pour comprendre (pas la solution complète)
4. Suggère une notion à revoir si nécessaire

Ton : bienveillant, clair, motivant
Format : 3-4 phrases courtes
Maximum : 100 mots";

        return $this->genererReponse($prompt, 300);
    }

    public function repondreQuestion(string $question, string $contexteCours, string $niveau): string
{
    $prompt = "CONTEXTE DU COURS :
{$contexteCours}

NIVEAU : {$niveau}

QUESTION DE L'ÉLÈVE : {$question}

INSTRUCTIONS STRICTES :
- NE DIS JAMAIS \"C'est une excellente question\"
- NE DIS JAMAIS \"Bonjour cher élève\"  
- NE DIS JAMAIS de formules de politesse
- VA DIRECTEMENT À LA RÉPONSE
- Utilise UNIQUEMENT les informations du CONTEXTE DU COURS ci-dessus
- Réponds en 2-4 phrases claires et simples
- Donne des exemples concrets si demandé
- Maximum 300 mots

EXEMPLE DE BONNE RÉPONSE :
Question : \"C'est quoi un nombre décimal ?\"
Réponse : \"Un nombre décimal est un nombre qui contient une virgule. Il est composé d'une partie entière (avant la virgule) et d'une partie décimale (après la virgule). Par exemple : 3,5 ou 12,75. La partie avant la virgule représente les unités complètes, et la partie après représente une fraction de l'unité.\"

Maintenant réponds à la question ci-dessus :";

    return $this->genererReponse($prompt, 400);
}
    public function genererRecommandations(array $stats, string $niveau, string $prenom): string
    {
        $statsText = json_encode($stats, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        $prompt = "Tu es un tuteur motivant pour {$prenom}, élève de {$niveau}.

Statistiques de progression :
{$statsText}

Génère des recommandations personnalisées :
1. Commence par un message encourageant personnalisé
2. Identifie 1-2 points forts (avec emojis positifs)
3. Identifie 1-2 points à améliorer (de façon constructive)
4. Suggère 3 actions concrètes pour progresser
5. Termine par un message motivant

Ton : amical, encourageant, personnalisé
Format : Structuré avec des emojis
Maximum : 200 mots";

        return $this->genererReponse($prompt, 600);
    }
}
