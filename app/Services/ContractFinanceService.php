<?php


namespace App\Services;


use App\Contract;
use App\Finance;

class ContractFinanceService implements Interfaces\ContractFinanceServiceInterface
{

    public function createContract($contractData)
    {
        $contract = new Contract($contractData);
        $contract->total_contract = $contract->contract_price;


        return $contract;
    }

    public function createFinance($contract)
    {
        $finance = new Finance();

        $contract_price = $contract->contract_price;
        $project_price = $contract_price / 1.1;
        $gst = $project_price * 0.10;
        $total_contract = $contract->total_contract;
        $deposit = $contract->deposit_amount;
        $balance = $total_contract - $deposit;

        $finance->project_price = $project_price;
        $finance->gst = $gst;
        $finance->contract_price = $contract_price;
        $finance->total_contract = $total_contract;
        $finance->deposit = $deposit;
        $finance->balance = $balance;

        return $finance;
    }

    public function updateContract($contract, $finance, $contractData)
    {
        // TODO: Implement updateContract() method.
    }
}
