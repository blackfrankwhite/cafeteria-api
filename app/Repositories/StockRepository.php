<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Models\Stock;

class StockRepository
{
    public function getStocks($perPage, $keyword)
    {
        return DB::table('entities')
            ->leftJoin('stocks', 'entities.id', '=', 'stocks.entity_id')
            ->leftJoin('accounting_records', 'entities.id', '=', 'accounting_records.entity_id')
            ->select(
                'entities.title as entity_title',
                DB::raw('IFNULL(SUM(stocks.amount), 0) - IFNULL(SUM(accounting_records.amount), 0) as current_amount'),
                DB::raw('CASE 
                            WHEN IFNULL(SUM(stocks.amount), 0) - IFNULL(SUM(accounting_records.amount), 0) < 0 
                            THEN "negative" 
                            ELSE "ok" 
                         END as status')
            )
            ->when($keyword, function ($query, $keyword) {
                return $query->where('entities.title', 'like', "%$keyword%");
            })
            ->groupBy('entities.id', 'entities.title')
            ->havingRaw('current_amount != 0')
            ->orderBy('current_amount', 'DESC')
            ->paginate();
    }    

    public function createStock($data)
    {
        return Stock::create($data);
    }

    public function updateStock($id, $data)
    {
        return Stock::where('id', $id)->update($data);
    }

    public function deleteStock($id)
    {
        return Stock::where('id', $id)->delete();
    }
}
