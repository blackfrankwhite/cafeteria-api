<?php

namespace App\Repositories;

use App\Models\Accounting;
use App\Models\AccountingRecord;
use Illuminate\Support\Facades\DB;

class AccountingRepository
{
    public function getAccountings()
    {
        return Accounting::all();
    }

    public function createAccounting($data)
    {
        return Accounting::create($data);
    }

    public function deleteAccounting($id)
    {
        return Accounting::destroy($id);
    }

    public function updateAccounting($id, $data)
    {
        return Accounting::where('id', $id)->update($data);
    }

    public function getAccountingByID($id)
    {
        return Accounting::find($id);
    }

    public function getAccountingRecords($accountingID)
    {
        return DB::table('accounting_records')->where('accounting_id', $accountingID)
            ->join('entities', 'accounting_records.entity_id', 'entities.id')
            ->join('accountings', 'accounting_records.accounting_id', 'accountings.id')
            ->select('accounting_records.*', 'accountings.title as accounting_title', 'entities.title as entity_title')
            ->get();
    }

    public function createAccountingRecord($id, $data)
    {
        $data['accounting_id'] = $id;

        return AccountingRecord::create($data);
    }

    public function deleteAccountingRecord($id)
    {
        return AccountingRecord::destroy($id);
    }

    public function updateAccountingRecord($id, $data)
    {
        return AccountingRecord::where('id', $id)->update($data);
    }

    public function getAccountingRecordByID($id)
    {
        return AccountingRecord::find($id);
    }
}
