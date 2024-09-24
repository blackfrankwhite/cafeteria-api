<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\AccountingRepository;

class AccountingController extends Controller
{
    protected $accountingRepository;

    public function __construct(AccountingRepository $accountingRepository)
    {
        $this->accountingRepository = $accountingRepository;
    }

    public function getAccountings()
    {
        return response()->json($this->accountingRepository->getAccountings());
    }

    public function createAccounting(Request $request)
    {
        return response()->json($this->accountingRepository->createAccounting($request->all()));
    }

    public function deleteAccounting($id)
    {
        return response()->json($this->accountingRepository->deleteAccounting($id));
    }

    public function updateAccounting(Request $request, $id)
    {
        return response()->json($this->accountingRepository->updateAccounting($id, $request->all()));
    }

    public function getAccountingByID($id)
    {
        return response()->json($this->accountingRepository->getAccountingByID($id));
    }

    public function getAccountingRecords($accountingID)
    {
        return response()->json($this->accountingRepository->getAccountingRecords($accountingID));
    }

    public function createAccountingRecord(Request $request, $accountingID, $recordID)
    {
        return response()->json($this->accountingRepository->createAccountingRecord($accountingID, $request->all()));
    }

    public function deleteAccountingRecord($accountingID, $recordID)
    {
        return response()->json($this->accountingRepository->deleteAccountingRecord($recordID));
    }

    public function updateAccountingRecord(Request $request, $accountingID, $recordID)
    {
        return response()->json($this->accountingRepository->updateAccountingRecord($recordID, $request->all()));
    }

    public function getAccountingRecordByID($accountingID, $recordID)
    {
        return response()->json($this->accountingRepository->getAccountingRecordByID($recordID));
    }
}
