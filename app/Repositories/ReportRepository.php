<?php


namespace App\Repositories;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportRepository implements Interfaces\ReportRepositoryInterface
{

    public function generateSalesSummary($queryParams)
    {

        $jobTypesSubQuery = DB::table('job_types')
            ->select('job_types.lead_id',
                'job_types.sales_staff_id',
                'job_types.product_id',
                'products.name as productName'
            )->join('products','job_types.product_id', '=', 'products.id' );

        $salesStaffSubQuery = DB::table('sales_staff')
            ->select( 'sales_staff.id',
                'sales_staff.first_name',
                'sales_staff.last_name',
                'franchises.franchise_number',
                'sales_staff.franchise_id'
            )->join('franchises', 'franchises.id', '=', 'sales_staff.franchise_id');

        $mainQuery = DB::table('leads')
            ->select('sales_staff.franchise_number as franchiseNumber')
            ->selectRaw("concat(sales_staff.first_name, ' ', sales_staff.last_name) as salesStaff")
            ->selectRaw("count(case appointments.outcome when 'success' then 1 else null end) as SuccessCount")
            ->selectRaw("count( IF (contracts.contract_price > 0 and appointments.outcome = 'success' , 1, null) ) as numberOfSales")
            ->selectRaw("count(leads.id) as numberOfLeads")
            ->selectRaw("(count( IF (contracts.contract_price > 0 and appointments.outcome = 'success' , 1, null) ) / count(leads.id)) * 100 as conversionRate")
            ->selectRaw("avg(contracts.contract_price) as averageSalesPrice")
            ->selectRaw("sum(contracts.total_contract) as totalContracts")
            ->leftJoin('appointments', 'appointments.lead_id', '=', 'leads.id')
            ->leftJoin('contracts', 'contracts.lead_id', '=', 'leads.id')
            ->leftJoin('job_types','job_types.lead_id', '=', 'leads.id' )
            ->leftJoinSub($salesStaffSubQuery, 'sales_staff', function ($join){
                $join->on('job_types.sales_staff_id', '=', 'sales_staff.id');
            });

            if($queryParams['start_date'] !== null && $queryParams['end_date'] !== null){

                $mainQuery = $mainQuery
                    ->whereBetween('leads.lead_date', [$queryParams['start_date'], $queryParams['end_date']]);
            }

            if(key_exists("franchise_id", $queryParams) && $queryParams['franchise_id'] !== ""){

                $mainQuery = $mainQuery->where('sales_staff.franchise_id',$queryParams['franchise_id'] );
            }


            if(key_exists("sales_staff_id", $queryParams) && $queryParams['sales_staff_id'] !== ""){

                $mainQuery = $mainQuery->where('sales_staff.id',$queryParams['sales_staff_id'] );
            }

            $mainQuery = $mainQuery->groupBy([
                'sales_staff.last_name',
                'sales_staff.first_name',
                'sales_staff.franchise_number',
            ]);


        return $mainQuery->get();




    }


    public function generateProductSalesSummary($queryParams)
    {

        $leadSubQuery = DB::table('leads')
            ->select('leads.id',
                'job_types.product_id',
                'appointments.outcome',
                'contracts.contract_price',
                'contracts.total_contract'
            )
            ->join('job_types','job_types.lead_id', '=', 'leads.id' )
            ->join('appointments','appointments.lead_id', '=', 'leads.id' )
            ->leftJoin('contracts','contracts.lead_id', '=', 'leads.id' );


        if($queryParams['start_date'] !== null && $queryParams['end_date'] !== null){
            $leadSubQuery = $leadSubQuery->whereBetween('leads.lead_date',[$queryParams['start_date'], $queryParams['end_date']]);
        }


        $mainQuery = DB::table('products')
            ->select('products.name')
            ->selectRaw("count(case leads.outcome when 'success' then 1 else null end) as SuccessCount")
            ->selectRaw("count(leads.id) as numberOfLeads")
            ->selectRaw("count( IF (leads.contract_price > 0 and leads.outcome = 'success' , 1, null) ) as numberOfSales")
            ->selectRaw("count(leads.id) as numberOfLeads")
            ->selectRaw("(count( IF (leads.contract_price > 0 and leads.outcome = 'success' , 1, null) ) / count(leads.id)) * 100 as conversionRate")
            ->selectRaw("avg(leads.contract_price) as averageSalesPrice")
            ->selectRaw("sum(leads.total_contract) as totalContracts")
            ->leftJoinSub($leadSubQuery, 'leads', function ($join){
                $join->on('products.id', '=', 'leads.product_id');
            });

        if(key_exists("product_id", $queryParams) && $queryParams['product_id'] !== ""){

          $mainQuery = $mainQuery->where('products.id',$queryParams['product_id'] );
        }

        $mainQuery = $mainQuery->groupBy([
            'products.name',
        ]);


        return $mainQuery->get();

    }
}
