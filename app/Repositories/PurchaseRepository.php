<?php

namespace App\Repositories;

use App\Models\Purchase;
use App\Models\PurchaseRecord;
use Illuminate\Support\Facades\DB;

class PurchaseRepository
{
    public function getPurchases($perPage = 10, $startDate = null, $endDate = null)
    {
        $purchases = $this->getPaginatedPurchases($perPage, $startDate, $endDate);
        $totalAmounts = $this->getTotalPurchaseAmounts($startDate, $endDate);

        $purchases = $purchases->toArray();
        $purchases['total_amount'] = $totalAmounts->total_amount ?? 0;
        $purchases['total_price'] = $totalAmounts->total_price ?? 0;
    
        return $purchases;
    }

    public function getPaginatedPurchases($perPage = 10, $startDate = null, $endDate = null)
    {
        return Purchase::leftJoin('purchase_records', 'purchases.id', 'purchase_records.purchase_id')
            ->select(
                'purchases.*', 
                DB::raw('SUM(purchase_records.price * purchase_records.amount) as total_price'),
                DB::raw('SUM(purchase_records.amount) as total_amount')
            )
            ->when($startDate, function ($query, $startDate) {
                return $query->where('purchases.date', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                return $query->where('purchases.date', '<=', $endDate);
            })
            ->groupBy('purchases.id')
            ->orderBy('purchases.id', 'DESC')
            ->paginate($perPage);
    }

    public function getTotalPurchaseAmounts($startDate = null, $endDate = null)
    {
        return Purchase::leftJoin('purchase_records', 'purchases.id', 'purchase_records.purchase_id')
            ->select(
                DB::raw('SUM(purchase_records.price * purchase_records.amount) as total_price'),
                DB::raw('SUM(purchase_records.amount) as total_amount')
            )
            ->when($startDate, function ($query, $startDate) {
                return $query->where('purchases.date', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                return $query->where('purchases.date', '<=', $endDate);
            })
            ->first();
    }

    public function createPurchase($data)
    {
         return Purchase::create($data);
    }

    public function deletePurchase($id)
    {
        return Purchase::destroy($id);
    }

    public function updatePurchase($id, $data)
    {
        return Purchase::where('id', $id)->update($data);
    }

    public function getPurchaseByID($id)
    {
        return Purchase::LeftJoin('purchase_records', 'purchases.id', 'purchase_records.purchase_id')
            ->select(
                'purchases.*', 
                DB::raw('SUM(purchase_records.price * purchase_records.amount) as total_price'),
                DB::raw('SUM(purchase_records.amount) as total_amount')
                )
            ->groupBy('purchases.id')
            ->find($id);
    }

    public function getPurchaseRecords($purchaseID)
    {
        return DB::table('purchase_records')
            ->LeftJoin('purchases', 'purchase_records.purchase_id', 'purchases.id')
            ->LeftJoin('entities', 'purchase_records.entity_id', 'entities.id')
            ->select(
                'purchase_records.*', 
                'purchases.title as purchase_title', 
                'entities.title as entity_title',
                DB::raw('SUM(purchase_records.price * purchase_records.amount) as total_price'),
                DB::raw('SUM(purchase_records.amount) as total_amount')
                )
            ->where('purchase_id', $purchaseID)
            ->groupBy('purchase_records.id')
            ->orderBy('purchase_records.id', 'DESC')
            ->get();
    }

    public function createPurchaseRecord($id, $data)
    {
        $data['purchase_id'] = $id;

        return PurchaseRecord::create($data);
    }

    public function deletePurchaseRecord($id)
    {
        return PurchaseRecord::destroy($id);
    }

    public function updatePurchaseRecord($id, $data)
    {
        return PurchaseRecord::where('id', $id)->update($data);
    }

    public function getPurchaseRecordByID($id)
    {
        return PurchaseRecord::find($id);
    }

    public function getPurchaseRecordByPurchaseID($purchaseID, $recordID)
    {
        return PurchaseRecord::where('purchase_id', $purchaseID)->where('id', $recordID)->first();
    }
}
