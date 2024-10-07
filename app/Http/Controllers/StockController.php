<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\StockRepository;

class StockController extends Controller
{
    protected $stockRepository;

    public function __construct(StockRepository $stockRepository)
    {
        $this->stockRepository = $stockRepository;
    }

    public function getStocks()
    {
        $perPage = request()->get('per_page');
        $keyword = request()->get('keyword');

        return response()->json($this->stockRepository->getStocks($perPage, $keyword));
    }

    public function createStock(Request $request)
    {
        return response()->json($this->stockRepository->createStock($request->all()));
    }

    public function deleteStock($id)
    {
        return response()->json($this->stockRepository->deleteStock($id));
    }

    public function updateStock(Request $request, $id)
    {
        return response()->json($this->stockRepository->updateStock($id, $request->all()));
    }
}
