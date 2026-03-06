<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http; 
use Illuminate\Support\Facades\Schema; // IMPORTANTE: Adicionar isto
use App\Models\Sale;

class FetchSales extends Command
{
    protected $signature = 'fetch:sales';
    protected $description = 'Sync sales data from the external API';

    public function handle()
    {
        $key = 'E6kUTYrYwZq2tN4QEtyzsbEBk3ie';
        $url = 'http://109.73.206.144:6969/api/sales';
        $page = 1;
        $maxPages = 1;

        $this->info("Starting Sales import...");

        // Pegamos as colunas uma vez fora do loop para ser mais rápido
        $columns = Schema::getColumnListing('sales');
        $columnsFlip = array_flip($columns);

        while ($page <= $maxPages) {
            $this->info("Fetching page $page...");
            $response = Http::get($url, [
                'key' => $key,
                'page' => $page,
                'dateFrom' => '2024-01-01',
                'dateTo'   => '2026-12-31',
                'limit' => 500
            ]);

            if ($response->failed()) {
                $this->error("API Connection failed at page $page.");
                break;
            }

            $items = $response->json()['data'] ?? [];

            if (empty($items)) {
                break;
            }

            foreach ($items as $item) {
                if (!isset($item['sale_id'])) continue;

                // Filtramos o item para manter apenas o que existe na DB
                $dataToSave = array_intersect_key($item, $columnsFlip);

                Sale::updateOrCreate(
                    ['sale_id' => $item['sale_id']], 
                    $dataToSave // CORREÇÃO: Usar o dado filtrado aqui
                );
            }

            if (count($items) < 500) {
                break;
            }

            $page++;
        }

        $this->info("Success! Sales table is up to date.");
    }
}