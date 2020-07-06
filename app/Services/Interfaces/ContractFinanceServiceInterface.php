<?php


namespace App\Services\Interfaces;


interface ContractFinanceServiceInterface
{
    public function createContract($contractData);

    public function createFinance($contract);

    public function updateContract($contract, $finance, $contractData);


}
