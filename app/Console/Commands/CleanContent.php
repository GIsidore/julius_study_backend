<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanContent extends Command
{
    protected $signature = 'content:clean';
    protected $description = 'Vide les tables de contenu pédagogique';

    public function handle()
    {
        $this->info('🗑️  Nettoyage du contenu...');
        
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        $tables = [
            'reponses_eleves',
            'progression_lecons',
            'performance_eleve',
            'corrections',
            'exercices',
            'lecons',
            'chapitres',
        ];
        
        foreach ($tables as $table) {
            DB::table($table)->truncate();
            $this->info("✅ Table '{$table}' vidée");
        }
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->info('🎉 Nettoyage terminé !');
        $this->info('📊 Vérification :');
        $this->info('   Chapitres : ' . DB::table('chapitres')->count());
        $this->info('   Leçons : ' . DB::table('lecons')->count());
        $this->info('   Exercices : ' . DB::table('exercices')->count());
        
        return 0;
    }
}
