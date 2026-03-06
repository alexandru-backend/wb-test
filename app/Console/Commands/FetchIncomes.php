<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http; 
use Illuminate\Support\Facades\Schema; // IMPORTANTE: Adicionar isto
use App\Models\Income;
use Carbon\Carbon;

class FetchIncomes extends Command
{
    protected $signature = 'fetch:incomes';
    protected $description = 'Sync incomes data from the external API';

    public function handle()
    {
        $key = 'E6kUTYrYwZq2tN4QEtyzsbEBk3ie';
        $url = 'http://109.73.206.144:6969/api/incomes'; 
        $page = 1;
        $maxPages = 1;

        $this->info("Starting Incomes import...");

        // Pegamos as colunas da tabela uma vez para performance
        $columns = Schema::getColumnListing('incomes');
        $columnsFlip = array_flip($columns);

        while ($page <= $maxPages) {
            $this->info("Fetching page $page...");
            
            $response = Http::get($url, [
                'key' => $key,
                'page' => $page,
                'dateFrom' => '2024-01-01',
                'dateTo'   => '2026-03-05',
                'limit' => 500
            ]);

            if ($response->failed()) {
                $this->error("API Connection failed at page $page. Status: " . $response->status());
                break;
            }

            $items = $response->json()['data'] ?? [];

            if (empty($items)) {
                break;
            }

            foreach ($items as $item) {
                if (!isset($item['income_id'])) continue;

                // FILTRO: Mantém apenas o que existe na tua Migration
                $dataToSave = array_intersect_key($item, $columnsFlip);

                Income::updateOrCreate(
                    ['income_id' => $item['income_id']], 
                    $dataToSave // USAR A VARIÁVEL FILTRADA AQUI
                );
            }

            if (count($items) < 500) {
                break;
            }

            $page++;
        }

        $this->info("Success! Incomes table is up to date.");
    }
}