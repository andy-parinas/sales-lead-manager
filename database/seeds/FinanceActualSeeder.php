<?php

use App\Contract;
use App\Services\Interfaces\ContractFinanceServiceInterface;
use Illuminate\Database\Seeder;

class FinanceActualSeeder extends Seeder
{

    protected $contractService;

    public function __construct(ContractFinanceServiceInterface $contractService)
    {
        $this->contractService = $contractService;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $contracts = Contract::all();

        foreach ($contracts as $contract){


            $finance = $this->contractService->createFinance($contract);

            $lead = $contract->lead;

            $lead->finance()->save($finance);

            print "Finance Created for Lead {$lead->id} LeadNumber: {$lead->lead_number} \n";

        }


    }
}
