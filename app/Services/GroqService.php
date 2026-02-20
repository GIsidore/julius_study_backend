<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqService
{
    private $apiKey;
    private $apiUrl = 'https://api.groq.com/openai/v1/chat/completions';
    private $model = 'llama-3.3-70b-versatile'; // Modèle ultra rapide

    public function __construct()
    {
        $this->apiKey = env('GROQ_API_KEY');
    }

    public function genererReponse(string $prompt, int $maxTokens = 500): string
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->apiUrl, [
                    'model' => $this->model,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Tu es un tuteur pédagogue et patient. Réponds de façon claire et concise.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => $maxTokens,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['choices'][0]['message']['content'] ?? 'Désolé, je ne peux pas répondre.';
            }

            Log::error('Erreur Groq API', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            return 'Désolé, une erreur est survenue.';
            
        } catch (\Exception $e) {
            Log::error('Exception Groq', ['message' => $e->getMessage()]);
            return 'Erreur technique : ' . $e->getMessage();
        }
    }

    public function repondreQuestion(string $question, string $contexteCours, string $niveau): string
    {
        $prompt = "CONTEXTE DU COURS :
{$contexteCours}

NIVEAU : {$niveau}

QUESTION DE L'ÉLÈVE : {$question}

INSTRUCTIONS :
- Réponds DIRECTEMENT sans formule de politesse
- Utilise UNIQUEMENT les informations du contexte
- Explique clairement en 3-5 phrases
- Donne des exemples concrets
- Maximum 150 mots

Réponds maintenant :";

        return $this->genererReponse($prompt, 400);
    }

    public function analyserErreur(string $question, string $reponseEleve, string $reponseCorrecte, string $niveau): string
    {
        $prompt = "Tu es un tuteur pour un élève de {$niveau}.

Question : {$question}
Réponse de l'élève : {$reponseEleve}
Bonne réponse : {$reponseCorrecte}

Génère un feedback bienveillant qui :
1. Encourage l'élève
2. Explique l'erreur
3. Donne un indice
4. Suggère une notion à revoir

Maximum 100 mots, ton motivant.";

        return $this->genererReponse($prompt, 300);
    }

    public function genererRecommandations(array $stats, string $niveau, string $prenom): string
    {
        $statsText = json_encode($stats, JSON_PRETTY_PRINT);
        
        $prompt = "Tu es un tuteur motivant pour {$prenom}, élève de {$niveau}.

Statistiques :
{$statsText}

Génère des recommandations personnalisées :
1. Message encourageant
2. Points forts (avec emojis)
3. Points à améliorer (constructif)
4. 3 actions concrètes

Ton amical, maximum 200 mots.";

        return $this->genererReponse($prompt, 600);
    }
}
