<?php

namespace App\Repositories;

use App\Models\Purchase;
use App\Models\PurchaseRecord;
use Illuminate\Support\Facades\DB;

class PurchaseRepository
{
   public function getPurchases()
   {
       return Purchase::all();
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
        return Purchase::find($id);
    }

    public function getPurchaseRecords($purchaseID)
    {
        return DB::table('purchase_records')
            ->join('purchases', 'purchase_records.purchase_id', 'purchases.id')
            ->select('purchase_records.*', 'purchases.title as title')
            ->where('purchase_id', $purchaseID)
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
