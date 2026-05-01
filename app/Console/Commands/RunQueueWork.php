<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

#[Signature('queue:run-jobs')]
#[Description('Processa a fila de trabalhos via Cronjob na hospedagem compartilhada')]
class RunQueueWork extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Roda a fila processando o que tiver e depois encerra (sem ficar escutando em loop infinito)
        // Isso é o ideal para CronJobs em hospedagens compartilhadas (Cpanel/Hostinger)
        $this->info('Processando fila...');

        Artisan::call('queue:work', [
            '--stop-when-empty' => true,
        ]);

        $this->info('Processamento concluído!');
    }
}
