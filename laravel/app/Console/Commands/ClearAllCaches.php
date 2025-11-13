<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearAllCaches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-all {--reload : Regenerar el autoload de Composer}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpia todas las cachés de la aplicación y opcionalmente recarga el autoload';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 Iniciando limpieza completa de cachés...');
        $this->newLine();

        // Limpiar caché de aplicación
        $this->call('cache:clear');
        $this->info('✓ Caché de aplicación limpiada');

        // Limpiar caché de configuración
        $this->call('config:clear');
        $this->info('✓ Caché de configuración limpiada');

        // Limpiar caché de rutas
        $this->call('route:clear');
        $this->info('✓ Caché de rutas limpiada');

        // Limpiar caché de vistas
        $this->call('view:clear');
        $this->info('✓ Caché de vistas limpiada');

        // Limpiar caché de eventos (si existe)
        if (method_exists($this, 'callSilent') && $this->callSilent('event:clear') === 0) {
            $this->info('✓ Caché de eventos limpiada');
        }

        // Limpiar caché compilada
        $this->call('clear-compiled');
        $this->info('✓ Archivos compilados eliminados');

        // Optimizar autoloader si se especifica la flag
        if ($this->option('reload')) {
            $this->newLine();
            $this->info('🔄 Regenerando autoload de Composer...');
            
            exec('composer dump-autoload -o 2>&1', $output, $returnCode);
            
            if ($returnCode === 0) {
                $this->info('✓ Autoload regenerado correctamente');
            } else {
                $this->error('✗ Error al regenerar el autoload');
                foreach ($output as $line) {
                    $this->line($line);
                }
            }
        }

        $this->newLine();
        $this->info('✅ Limpieza completada con éxito');

        return Command::SUCCESS;
    }
}