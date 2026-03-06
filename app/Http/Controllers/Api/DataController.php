<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Order;
use App\Models\Stock;
use App\Models\Income;

class DataController extends Controller
{
    private $validToken = 'E6kUTYrYwZq2tN4QEtyzsbEBk3ie';

    public function getSales(Request $request) {
        return $this->process($request, Sale::query());
    }

    public function getOrders(Request $request) {
        return $this->process($request, Order::query());
    }

    public function getIncomes(Request $request) {
        return $this->process($request, Income::query());
    }

    public function getStocks(Request $request) {
        $query = Stock::query();
        // Ajustado para 'download_date' que é o que está na tua Migration
        if ($request->has('dateFrom')) {
            $query->where('download_date', $request->dateFrom);
        }
        return $this->process($request, $query);
    }

    private function process(Request $request, $query) {
        if ($request->query('key') !== $this->validToken) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Identifica qual a tabela estamos a consultar
        $isStocks = $request->segment(2) === 'stocks';
        $dateColumn = $isStocks ? 'download_date' : 'date';

        // Filtro dateFrom
        if ($request->has('dateFrom')) {
            // Se for Stocks, usamos '=', se for o resto usamos '>='
            $operator = $isStocks ? '=' : '>=';
            $query->where($dateColumn, $operator, $request->dateFrom);
        }

        // Filtro dateTo (não se aplica a stocks conforme o enunciado)
        if ($request->has('dateTo') && !$isStocks) {
            $query->where($dateColumn, '<=', $request->dateTo);
        }

        $limit = $request->query('limit', 500);
        return $query->paginate($limit);
    }
}