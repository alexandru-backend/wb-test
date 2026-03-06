<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema; // Adicionado para segurança
use App\Models\Stock;
use Carbon\Carbon;

class FetchStocks extends Command
{
    protected $signature = 'fetch:stocks';
    protected $description = 'Fetch stock data from the external API';

    public function handle()
    {
        $key = 'E6kUTYrYwZq2tN4QEtyzsbEBk3ie';
        $url = 'http://109.73.206.144:6969/api/stocks';
        $page = 1;
        $maxPages = 1;

        $this->info("Starting Stocks import...");

        // Pegamos as colunas da tabela para filtrar os dados da API
        $columns = Schema::getColumnListing('stocks');
        $columnsFlip = array_flip($columns);

        while ($page <= $maxPages) {
            $this->info("Fetching page $page...");
            
            $response = Http::get($url, [
                'key' => $key,
                'page' => $page,
                'dateFrom' => now()->format('Y-m-d'), // Stocks é apenas para o dia atual
                'limit' => 500
            ]);

            if ($response->failed()) {
                $this->error("Failed to connect to the API at page $page.");
                break;
            }

            $items = $response->json()['data'] ?? [];

            if (empty($items)) {
                $this->info("No more data found. Import finished.");
                break;
            }

            foreach ($items as $item) {
                // Mapeamento: a API manda 'nm_id', a tua DB tem 'nmId'
                // Vamos ajustar os nomes para baterem com a tua migration
                $mappedItem = [
                    'nmId'            => $item['nm_id'] ?? null,
                    'warehouseName'   => $item['warehouse_name'] ?? null,
                    'subject'         => $item['subject'] ?? null,
                    'brand'           => $item['brand'] ?? null,
                    'quantity'        => $item['quantity'] ?? 0,
                    'inWayToClient'   => $item['in_way_to_client'] ?? 0,
                    'inWayFromClient' => $item['in_way_from_client'] ?? 0,
                    'category'        => $item['category'] ?? null,
                    'price'           => $item['price'] ?? 0,
                    'discount'        => $item['discount'] ?? 0,
                    'download_date'   => now()->format('Y-m-d'),
                ];

                // Filtro de segurança (intersect)
                $dataToSave = array_intersect_key($mappedItem, $columnsFlip);

                Stock::updateOrCreate(
                    [
                        'nmId'          => $mappedItem['nmId'], 
                        'warehouseName' => $mappedItem['warehouseName']
                    ],
                    $dataToSave
                );
            }

            // Se vierem menos de 500, é a última página
            if (count($items) < 500) break;

            $page++;
        }

        $this->info("Success! All stock data has been synced to the database.");
    }
}