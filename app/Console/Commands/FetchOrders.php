<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http; 
use Illuminate\Support\Facades\Schema;
use App\Models\Order; // Certifica-te que o Model se chama Order

class FetchOrders extends Command
{
    // A assinatura que vais usar no terminal: php artisan fetch:orders
    protected $signature = 'fetch:orders';
    protected $description = 'Sync orders data from the external API';

    public function handle()
    {
        $key = 'E6kUTYrYwZq2tN4QEtyzsbEBk3ie';
        $url = 'http://109.73.206.144:6969/api/orders'; // Endpoint de ORDERS
        $page = 1;
        $maxPages = 1; // Mantém baixo para o teste ser rápido

        $this->info("Starting Orders import...");

        // Verifica se a tabela orders existe antes de continuar
        if (!Schema::hasTable('orders')) {
            $this->error("Table 'orders' not found. Run migrations first!");
            return;
        }

        // Pegamos as colunas da tabela ORDERS
        $columns = Schema::getColumnListing('orders');
        $columnsFlip = array_flip($columns);

        while ($page <= $maxPages) {
            $this->info("Fetching Orders page $page...");
            
            $response = Http::get($url, [
                'key'      => $key,
                'page'     => $page,
                'dateFrom' => '2024-01-01',
                'dateTo'   => '2026-12-31',
                'limit'    => 500
            ]);

            if ($response->failed()) {
                $this->error("API Connection failed at page $page.");
                break;
            }

            $items = $response->json()['data'] ?? [];

            if (empty($items)) {
                $this->info("No more orders found.");
                break;
            }

            foreach ($items as $item) {
                // Nas Orders, o ID pode ser 'g_number' ou 'order_id' 
                // Ajusta conforme o que a API envia e o que tens na DB
                $orderId = $item['g_number'] ?? $item['order_id'] ?? null;

                if (!$orderId) continue;

                // Filtra os campos para baterem com a tua migration de orders
                $dataToSave = array_intersect_key($item, $columnsFlip);

                Order::updateOrCreate(
                    ['g_number' => $orderId], // Chave única (ajusta para 'order_id' se preferires)
                    $dataToSave
                );
            }

            if (count($items) < 500) break;
            $page++;
        }

        $this->info("Success! Orders table is up to date.");
    }
}