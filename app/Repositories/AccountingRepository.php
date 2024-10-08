<?php

namespace App\Repositories;

use App\Models\Accounting;
use App\Models\AccountingRecord;
use Illuminate\Support\Facades\DB;

class AccountingRepository
{
    public function getAccountings($perPage = 10, $startDate = null, $endDate = null)
    {
        $accountings = $this->getPaginatedAccountings($perPage, $startDate, $endDate);

        $totalAmounts = $this->getTotalAccountingAmounts($startDate, $endDate);
    
        $accountings = $accountings->toArray();
        $accountings['total_amount'] = $totalAmounts->total_amount ?? 0;
        $accountings['total_price'] = $totalAmounts->total_price ?? 0;
    
        return $accountings;
    }

    public function getPaginatedAccountings($perPage = 10, $startDate = null, $endDate = null)
    {
        return Accounting::leftJoin('accounting_records', 'accountings.id', 'accounting_records.accounting_id')
            ->leftJoin('entities', 'accounting_records.entity_id', 'entities.id')
            ->select(
                'accountings.*', 
                DB::raw('SUM(accounting_records.price * accounting_records.amount) as total_price'),
                DB::raw('SUM(accounting_records.amount) as total_amount')
            )
            ->when($startDate, function ($query, $startDate) {
                return $query->where('accountings.created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                return $query->where('accountings.created_at', '<=', $endDate);
            })
            ->groupBy('accountings.id')
            ->orderBy('accountings.id', 'DESC')
            ->paginate($perPage);
    }

    public function getTotalAccountingAmounts($startDate = null, $endDate = null)
    {
        return Accounting::leftJoin('accounting_records', 'accountings.id', 'accounting_records.accounting_id')
            ->select(
                DB::raw('SUM(accounting_records.price * accounting_records.amount) as total_price'),
                DB::raw('SUM(accounting_records.amount) as total_amount')
            )
            ->when($startDate, function ($query, $startDate) {
                return $query->where('accountings.created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                return $query->where('accountings.created_at', '<=', $endDate);
            })
            ->first();
    }    

    public function createAccounting($data)
    {
        return Accounting::create($data);
    }

    public function deleteAccounting($id)
    {
        AccountingRecord::where('accounting_id', $id)->delete();

        return Accounting::destroy($id);
    }

    public function updateAccounting($id, $data)
    {
        return Accounting::where('id', $id)->update($data);
    }

    public function getAccountingByID($id)
    {
        return Accounting::leftJoin('accounting_records', 'accountings.id', 'accounting_records.accounting_id')
            ->select(
                'accountings.*', 
                DB::raw('SUM(accounting_records.price * accounting_records.amount) as total_price'),
                DB::raw('SUM(accounting_records.amount) as total_amount')
                )
            ->groupBy('accountings.id')
            ->find($id);
    }

    public function getAccountingRecords($accountingID)
    {
        return DB::table('accounting_records')->where('accounting_id', $accountingID)
            ->leftJoin('entities', 'accounting_records.entity_id', 'entities.id')
            ->leftJoin('accountings', 'accounting_records.accounting_id', 'accountings.id')
            ->select(
                'accounting_records.*', 
                'accountings.title as accounting_title', 
                'entities.title as entity_title',
                DB::raw('accounting_records.price * accounting_records.amount as total_price'),
                DB::raw('SUM(accounting_records.amount) as total_amount')
                )
            ->groupBy('accounting_records.id')
            ->orderBy('accounting_records.id', 'DESC')
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
