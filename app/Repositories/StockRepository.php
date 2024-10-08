<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Models\Stock;

class StockRepository
{
    public function getStocks($perPage, $startDate, $endDate, $keyword)
    {    
        return Stock::orderBy('id', 'DESC')
            ->with('entity')
            ->when($startDate, function ($query, $startDate) {
                return $query->where('date', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                return $query->where('date', '<=', $endDate);
            })
            ->when($keyword, function ($query, $keyword) {
                return $query->whereHas('entity', function ($query) use ($keyword) {
                    $query->where('title', 'like', "%$keyword%");
                });
            })
            ->paginate($perPage);
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
