<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\IndexType;
use App\Models\IndexValue;
use Carbon\Carbon;

class FetchIclValues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'icl:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch ICL values from BCRA API and calculate monthly variation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting ICL fetch from BCRA...');

        $indexType = IndexType::firstOrCreate(['name' => 'ICL']);
        
        // BCRA ID Variable for ICL
        $iclId = 7988; 
        
        $hasta = Carbon::now()->format('Y-m-d');
        // Traer desde el mes anterior para poder calcular la variación entre el valor del día de hoy (o el último disponible del mes) y el del mes anterior.
        $desde = Carbon::now()->subMonths(2)->startOfMonth()->format('Y-m-d');
        
        $url = "https://api.bcra.gob.ar/estadisticas/v4.0/monetarias/{$iclId}/datos?desde={$desde}&hasta={$hasta}";
        
        try {
            $response = Http::withoutVerifying()->get($url);
            
            if ($response->successful()) {
                $data = $response->json('results');
                
                if (empty($data)) {
                    $this->warn('No data returned from BCRA for ICL.');
                    return;
                }

                // Ordenar los datos por fecha ascendente para asegurar la iteración correcta.
                usort($data, function($a, $b) {
                    return strtotime($a['fecha']) - strtotime($b['fecha']);
                });

                // Agrupar por mes y año para obtener el último valor de cada mes.
                $monthlyValues = [];
                foreach ($data as $item) {
                    $date = Carbon::parse($item['fecha']);
                    $key = $date->format('Y-m');
                    // Sobrescribimos siempre, como viene ordenado cronológicamente, nos quedamos con el último valor reportado en ese mes.
                    $monthlyValues[$key] = [
                        'date' => $item['fecha'],
                        'value' => $item['valor']
                    ];
                }
                
                // Ahora calculamos la variación mes a mes.
                $previousValue = null;
                $previousMonthKey = null;

                foreach ($monthlyValues as $monthKey => $dataPoint) {
                    $currentValue = $dataPoint['value'];
                    
                    if ($previousValue !== null) {
                        // Variación = ((ICL actual / ICL mes anterior) - 1) * 100
                        $variation = (($currentValue / $previousValue) - 1) * 100;
                        
                        $dateForDB = Carbon::createFromFormat('Y-m', $monthKey)->startOfMonth()->format('Y-m-d');
                        
                        IndexValue::updateOrCreate(
                            [
                                'index_type_id' => $indexType->id,
                                'date' => $dateForDB,
                            ],
                            [
                                'value' => round($variation, 2)
                            ]
                        );
                        
                        $this->info("ICL for {$monthKey} calculated: " . round($variation, 2) . "%");
                    }
                    
                    $previousValue = $currentValue;
                    $previousMonthKey = $monthKey;
                }

                $this->info('ICL updated successfully.');
            } else {
                $this->error('Failed to fetch ICL from BCRA. HTTP Status: ' . $response->status());
            }
        } catch (\Exception $e) {
            $this->error('Exception fetching ICL: ' . $e->getMessage());
        }
    }
}
