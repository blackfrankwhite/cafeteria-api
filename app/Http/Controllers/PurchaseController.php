<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\PurchaseRepository;

class PurchaseController extends Controller
{
    protected $purchaseRepository;

    public function __construct(PurchaseRepository $purchaseRepository)
    {
        $this->purchaseRepository = $purchaseRepository;
    }

    public function getPurchases()
    {
        return response()->json($this->purchaseRepository->getPurchases());
    }

    public function createPurchase(Request $request)
    {
        return response()->json($this->purchaseRepository->createPurchase($request->all()));
    }

    public function deletePurchase($id)
    {
        return response()->json($this->purchaseRepository->deletePurchase($id));
    }

    public function updatePurchase(Request $request, $id)
    {
        return response()->json($this->purchaseRepository->updatePurchase($id, $request->all()));
    }

    public function getPurchaseByID($id)
    {
        return response()->json($this->purchaseRepository->getPurchaseByID($id));
    }

    public function getPurchaseRecords($purchaseID)
    {
        return response()->json($this->purchaseRepository->getPurchaseRecords($purchaseID));
    }

    public function createPurchaseRecord(Request $request, $purchaseID)
    {
        return response()->json($this->purchaseRepository->createPurchaseRecord($purchaseID, $request->all()));
    }

    public function deletePurchaseRecord($purchaseID, $recordID)
    {
        return response()->json($this->purchaseRepository->deletePurchaseRecord($recordID));
    }

    public function updatePurchaseRecord(Request $request, $purchaseID, $recordID)
    {
        return response()->json($this->purchaseRepository->updatePurchaseRecord($recordID, $request->all()));
    }

    public function getPurchaseRecordByID($purchaseID, $recordID)
    {
        return response()->json($this->purchaseRepository->getPurchaseRecordByID($recordID));
    }
}
