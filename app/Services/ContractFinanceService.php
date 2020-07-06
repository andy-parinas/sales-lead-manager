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

    public function updateContract($contract, $data)
    {
        $total_contract = $contract->total_contract;

        if($data['contract_price'] != $contract->contract_price){

            $total_contract = $data['contract_price']  + $contract->total_variation;
            $data['total_contract'] = $total_contract;
        }

        if($total_contract < 0 ){
            throw new \Exception("Update will cause negative value on  Total Contract");
        }


        $contract->update($data);

        return $contract;
    }

    public function updateFinance($finance, $contract)
    {
        $contract_price = $contract->contract_price;
        $project_price = $contract_price / 1.1;
        $gst = $project_price * 0.10;
        $total_contract = $contract->total_contract;
        $deposit = $contract->deposit_amount;
        $balance = $total_contract - $deposit - $finance->total_payment_made;

        $finance->project_price = $project_price;
        $finance->gst = $gst;
        $finance->contract_price = $contract_price;
        $finance->total_contract = $total_contract;
        $finance->deposit = $deposit;
        $finance->balance = $balance;

        $finance->save();

        return $finance;
    }
}
