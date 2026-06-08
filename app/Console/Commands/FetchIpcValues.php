<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\IndexType;
use App\Models\IndexValue;
use Carbon\Carbon;

class FetchIpcValues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ipc:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch IPC values from BCRA API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting IPC fetch from BCRA...');

        $indexType = IndexType::firstOrCreate(['name' => 'IPC']);
        
        // BCRA ID Variable for IPC (Variación mensual del índice de precios al consumidor)
        $ipcId = 27; 
        
        $hasta = Carbon::now()->format('Y-m-d');
        // Traer desde los últimos 24 meses
        $desde = Carbon::now()->subMonths(24)->startOfMonth()->format('Y-m-d');
        
        $url = "https://api.bcra.gob.ar/estadisticas/v4.0/monetarias/{$ipcId}?desde={$desde}&hasta={$hasta}";
        
        try {
            $response = Http::withoutVerifying()->get($url);
            
            if ($response->successful()) {
                $data = $response->json('results.0.detalle');
                
                if (empty($data)) {
                    $this->warn('No data returned from BCRA for IPC.');
                    return;
                }

                foreach ($data as $item) {
                    $date = Carbon::parse($item['fecha']);
                    $monthKey = $date->format('Y-m');
                    $variation = $item['valor']; // Ya viene como porcentaje
                    
                    $parts = explode('-', $monthKey);
                    
                    IndexValue::updateOrCreate(
                        [
                            'index_type_id' => $indexType->id,
                            'year' => $parts[0],
                            'month' => $parts[1],
                        ],
                        [
                            'percentage' => round($variation, 4)
                        ]
                    );
                    
                    $this->info("IPC for {$monthKey} fetched: " . round($variation, 2) . "%");
                }

                $this->info('IPC updated successfully.');
            } else {
                $this->error('Failed to fetch IPC from BCRA. HTTP Status: ' . $response->status());
            }
        } catch (\Exception $e) {
            $this->error('Exception fetching IPC: ' . $e->getMessage());
        }
    }
}
