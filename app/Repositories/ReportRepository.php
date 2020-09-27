<?php


namespace App\Repositories;


use App\SalesStaff;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportRepository implements Interfaces\ReportRepositoryInterface
{

    public function generateSalesSummary($queryParams)
    {


        $leadsQuery = DB::table('leads')
            ->select('leads.id as leadId',
                'job_types.sales_staff_id as salesStaffId',
                'appointments.outcome as outcome',
                'leads.lead_number as leadNumber',
                'leads.lead_date',
                'contracts.contract_price as contractPrice',
                'contracts.total_contract as totalContract'
            )
            ->leftJoin('job_types', 'job_types.lead_id', '=', 'leads.id')
            ->leftJoin('appointments', 'appointments.lead_id', '=', 'leads.id')
            ->leftJoin('contracts', 'contracts.lead_id', '=', 'leads.id');


        $mainQuery = DB::table('sales_staff')
            ->select('franchises.franchise_number as franchiseNumber')
            ->selectRaw("concat(sales_staff.first_name, ' ', sales_staff.last_name) as salesStaff")
            ->selectRaw("count( IF (leadsJoin.contractPrice > 0 and leadsJoin.outcome = 'success' , 1, null) ) as numberOfSales")
            ->selectRaw("count(leadsJoin.leadId) as numberOfLeads")
            ->selectRaw("(count( IF (leadsJoin.contractPrice > 0 and leadsJoin.outcome = 'success' , 1, null) ) / count(leadsJoin.leadId)) * 100 as conversionRate")
            ->selectRaw("avg(leadsJoin.contractPrice) as averageSalesPrice")
            ->selectRaw("sum(leadsJoin.contractPrice) as totalContracts")
            ->join('franchises', 'sales_staff.franchise_id', '=', 'franchises.id')
            ->leftJoinSub($leadsQuery, 'leadsJoin', function ($join){
                $join->on('sales_staff.id', '=', 'leadsJoin.salesStaffId');
            });


        if($queryParams['start_date'] !== null && $queryParams['end_date'] !== null){

                $mainQuery = $mainQuery
                    ->whereBetween('leadsJoin.lead_date', [$queryParams['start_date'], $queryParams['end_date']]);
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
                'franchises.franchise_number'
            ]);


        return $mainQuery->get();

    }


    public function generateSalesSummaryByFranchises($franchiseIds, $queryParams)
    {
        $leadsQuery = DB::table('leads')
            ->select('leads.id as leadId',
                'job_types.sales_staff_id as salesStaffId',
                'appointments.outcome as outcome',
                'leads.lead_number as leadNumber',
                'leads.lead_date',
                'contracts.contract_price as contractPrice',
                'contracts.total_contract as totalContract'
            )
            ->leftJoin('job_types', 'job_types.lead_id', '=', 'leads.id')
            ->leftJoin('appointments', 'appointments.lead_id', '=', 'leads.id')
            ->leftJoin('contracts', 'contracts.lead_id', '=', 'leads.id');


        $mainQuery = DB::table('sales_staff')
            ->select('franchises.franchise_number as franchiseNumber')
            ->selectRaw("concat(sales_staff.first_name, ' ', sales_staff.last_name) as salesStaff")
            ->selectRaw("count( IF (leadsJoin.contractPrice > 0 and leadsJoin.outcome = 'success' , 1, null) ) as numberOfSales")
            ->selectRaw("count(leadsJoin.leadId) as numberOfLeads")
            ->selectRaw("(count( IF (leadsJoin.contractPrice > 0 and leadsJoin.outcome = 'success' , 1, null) ) / count(leadsJoin.leadId)) * 100 as conversionRate")
            ->selectRaw("avg(leadsJoin.contractPrice) as averageSalesPrice")
            ->selectRaw("sum(leadsJoin.contractPrice) as totalContracts")
            ->join('franchises', 'sales_staff.franchise_id', '=', 'franchises.id')
            ->leftJoinSub($leadsQuery, 'leadsJoin', function ($join){
                $join->on('sales_staff.id', '=', 'leadsJoin.salesStaffId');
            })->where('sales_staff.status', SalesStaff::ACTIVE)
            ->whereIn('franchises.id', $franchiseIds);


        if($queryParams['start_date'] !== null && $queryParams['end_date'] !== null){

            $mainQuery = $mainQuery
                ->whereBetween('leadsJoin.lead_date', [$queryParams['start_date'], $queryParams['end_date']]);
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
            'franchises.franchise_number'
        ]);


        return $mainQuery->get();

    }


    public function generateSalesSummaryV2($queryParams)
    {

        $jobTypesSubQuery = DB::table('job_types')
            ->select('job_types.lead_id',
                'job_types.sales_staff_id',
                'job_types.product_id',
                'products.name as productName',
                'products.id as productId'
            )->join('products','job_types.product_id', '=', 'products.id' );


        $franchiseSubQuery = DB::table('franchises')
            ->select( 'sales_staff.id',
                'sales_staff.first_name',
                'sales_staff.last_name',
                'franchises.franchise_number',
                'sales_staff.franchise_id'
            )->join('franchises', 'franchises.id', '=', 'sales_staff.franchise_id');


        $mainQuery = DB::table('sales_staff')
            ->select('franchises.franchise_number as franchiseNumber',
                'job_types.productName',
                'job_types.productId',
                'sales_staff.first_name',
                'sales_staff.last_name')
            ->selectRaw("concat(sales_staff.first_name, ' ', sales_staff.last_name) as salesStaff")
            ->selectRaw("count(case appointments.outcome when 'success' then 1 else null end) as SuccessCount")
            ->selectRaw("count( IF (contracts.contract_price > 0 and appointments.outcome = 'success' , 1, null) ) as numberOfSales")
            ->selectRaw("count(leads.id) as numberOfLeads")
            ->selectRaw("(count( IF (contracts.contract_price > 0 and appointments.outcome = 'success' , 1, null) ) / count(leads.id)) * 100 as conversionRate")
            ->selectRaw("avg(contracts.contract_price) as averageSalesPrice")
            ->selectRaw("sum(contracts.total_contract) as totalContracts")
            ->leftJoin('appointments', 'appointments.sales_staff_id', '=', 'sales_staff.id')
            ->leftJoin('contracts', 'contracts.lead_id', '=', 'appointments.leads_id')
            ->leftJoinSub($jobTypesSubQuery, 'job_types', function ($join){
                $join->on('job_types.lead_id', '=', 'appointments.leads_id');
            })
            ->leftJoin('franchises', 'franchises.id', '=', 'sales_staff.franchise_id');

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


            if(key_exists("product_id", $queryParams) && $queryParams['product_id'] !== ""){

                $mainQuery = $mainQuery->where('job_types.productId',$queryParams['product_id'] );
            }

            $mainQuery = $mainQuery->groupBy([
                'sales_staff.last_name',
                'sales_staff.first_name',
                'sales_staff.franchise_number',
                'job_types.productName',
                'job_types.productId'
            ]);

            return $mainQuery->get();

    }


    public function generateSalesStaffProductSummary($queryParams){

        $jobTypeProductQuery = DB::table('job_types')
                ->select('job_types.lead_id',
                    'job_types.sales_staff_id',
                    'products.name as productName', 'products.id as productId')
                ->join('products', 'job_types.product_id', '=', 'products.id');


        $leadsQuery = DB::table('leads')
            ->select('leads.id as leadId',
                        'jobTypeJoin.sales_staff_id as salesStaffId',
                        'appointments.outcome as outcome',
                        'leads.lead_number as leadNumber',
                        'leads.lead_date',
                        'contracts.contract_price as contractPrice',
                        'contracts.total_contract as totalContract',
                        'jobTypeJoin.productName',
                        'jobTypeJoin.productId'
            )
            ->leftJoinSub($jobTypeProductQuery, 'jobTypeJoin', function ($join){
                $join->on('jobTypeJoin.lead_id', '=', 'leads.id');
            })
            ->leftJoin('appointments', 'appointments.lead_id', '=', 'leads.id')
            ->leftJoin('contracts', 'contracts.lead_id', '=', 'leads.id');


        $mainQuery = DB::table('sales_staff')
            ->select('franchises.franchise_number as franchiseNumber', 'leadsJoin.productName')
            ->selectRaw("concat(sales_staff.first_name, ' ', sales_staff.last_name) as salesStaff")
            ->selectRaw("count( IF (leadsJoin.contractPrice > 0 and leadsJoin.outcome = 'success' , 1, null) ) as numberOfSales")
            ->selectRaw("count(leadsJoin.leadId) as numberOfLeads")
            ->selectRaw("(count( IF (leadsJoin.contractPrice > 0 and leadsJoin.outcome = 'success' , 1, null) ) / count(leadsJoin.leadId)) * 100 as conversionRate")
            ->selectRaw("avg(leadsJoin.contractPrice) as averageSalesPrice")
            ->selectRaw("sum(leadsJoin.contractPrice) as totalContracts")
            ->join('franchises', 'sales_staff.franchise_id', '=', 'franchises.id')
            ->leftJoinSub($leadsQuery, 'leadsJoin', function ($join){
                $join->on('sales_staff.id', '=', 'leadsJoin.salesStaffId');
            });


        if($queryParams['start_date'] !== null && $queryParams['end_date'] !== null){

            $mainQuery = $mainQuery
                ->whereBetween('leadsJoin.lead_date', [$queryParams['start_date'], $queryParams['end_date']]);
        }

        if(key_exists("franchise_id", $queryParams) && $queryParams['franchise_id'] !== ""){

            $mainQuery = $mainQuery->where('sales_staff.franchise_id',$queryParams['franchise_id'] );
        }


        if(key_exists("sales_staff_id", $queryParams) && $queryParams['sales_staff_id'] !== ""){

            $mainQuery = $mainQuery->where('sales_staff.id',$queryParams['sales_staff_id'] );
        }


        if(key_exists("product_id", $queryParams) && $queryParams['product_id'] !== ""){

            $mainQuery = $mainQuery->where('leadsJoin.productId',$queryParams['product_id'] );
        }

        $mainQuery = $mainQuery->groupBy([
            'sales_staff.last_name',
            'sales_staff.first_name',
            'franchises.franchise_number',
            'leadsJoin.productName'
        ]);


        return $mainQuery->get();


    }


    public function generateSalesStaffProductSummaryByFranchises($franchiseIds, $queryParams)
    {
        $jobTypeProductQuery = DB::table('job_types')
            ->select('job_types.lead_id',
                'job_types.sales_staff_id',
                'products.name as productName', 'products.id as productId')
            ->join('products', 'job_types.product_id', '=', 'products.id');


        $leadsQuery = DB::table('leads')
            ->select('leads.id as leadId',
                'jobTypeJoin.sales_staff_id as salesStaffId',
                'appointments.outcome as outcome',
                'leads.lead_number as leadNumber',
                'leads.lead_date',
                'contracts.contract_price as contractPrice',
                'contracts.total_contract as totalContract',
                'jobTypeJoin.productName',
                'jobTypeJoin.productId'
            )
            ->leftJoinSub($jobTypeProductQuery, 'jobTypeJoin', function ($join){
                $join->on('jobTypeJoin.lead_id', '=', 'leads.id');
            })
            ->leftJoin('appointments', 'appointments.lead_id', '=', 'leads.id')
            ->leftJoin('contracts', 'contracts.lead_id', '=', 'leads.id');


        $mainQuery = DB::table('sales_staff')
            ->select('franchises.franchise_number as franchiseNumber', 'leadsJoin.productName')
            ->selectRaw("concat(sales_staff.first_name, ' ', sales_staff.last_name) as salesStaff")
            ->selectRaw("count( IF (leadsJoin.contractPrice > 0 and leadsJoin.outcome = 'success' , 1, null) ) as numberOfSales")
            ->selectRaw("count(leadsJoin.leadId) as numberOfLeads")
            ->selectRaw("(count( IF (leadsJoin.contractPrice > 0 and leadsJoin.outcome = 'success' , 1, null) ) / count(leadsJoin.leadId)) * 100 as conversionRate")
            ->selectRaw("avg(leadsJoin.contractPrice) as averageSalesPrice")
            ->selectRaw("sum(leadsJoin.contractPrice) as totalContracts")
            ->join('franchises', 'sales_staff.franchise_id', '=', 'franchises.id')
            ->leftJoinSub($leadsQuery, 'leadsJoin', function ($join){
                $join->on('sales_staff.id', '=', 'leadsJoin.salesStaffId');
            })->where('sales_staff.status', SalesStaff::ACTIVE)
            ->whereIn('franchises.id', $franchiseIds);


        if($queryParams['start_date'] !== null && $queryParams['end_date'] !== null){

            $mainQuery = $mainQuery
                ->whereBetween('leadsJoin.lead_date', [$queryParams['start_date'], $queryParams['end_date']]);
        }

        if(key_exists("franchise_id", $queryParams) && $queryParams['franchise_id'] !== ""){

            $mainQuery = $mainQuery->where('sales_staff.franchise_id',$queryParams['franchise_id'] );
        }


        if(key_exists("sales_staff_id", $queryParams) && $queryParams['sales_staff_id'] !== ""){

            $mainQuery = $mainQuery->where('sales_staff.id',$queryParams['sales_staff_id'] );
        }


        if(key_exists("product_id", $queryParams) && $queryParams['product_id'] !== ""){

            $mainQuery = $mainQuery->where('leadsJoin.productId',$queryParams['product_id'] );
        }

        $mainQuery = $mainQuery->groupBy([
            'sales_staff.last_name',
            'sales_staff.first_name',
            'franchises.franchise_number',
            'leadsJoin.productName'
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



    public function generateOutcomeSalesStaff($queryParams){

        $salesStaffQuery = DB::table('job_types')
            ->select(
                'job_types.lead_id',
                'sales_staff.first_name',
                'sales_staff.last_name'
            )->join('sales_staff', 'sales_staff.id', '=', 'job_types.sales_staff_id');


        $mainQuery = DB::table('leads')
            ->select(
                'appointments.outcome',
                'franchises.franchise_number'
            )
            ->selectRaw("concat(sales_staff.first_name, ' ', sales_staff.last_name) as salesStaff")
            ->selectRaw("count(leads.id) as numberOfLeads")
            ->join('appointments', 'appointments.lead_id', '=', 'leads.id')
            ->join('franchises', 'franchises.id', '=', 'leads.franchise_id')
            ->joinSub($salesStaffQuery, 'sales_staff', function ($join){
                $join->on('sales_staff.lead_id', '=', 'leads.id');
            });


        if($queryParams['start_date'] !== null && $queryParams['end_date'] !== null){

            $mainQuery = $mainQuery
                ->whereBetween('leads.lead_date', [$queryParams['start_date'], $queryParams['end_date']]);
        }

        if(key_exists("outcome", $queryParams) && $queryParams['outcome'] !== ""){

            $mainQuery = $mainQuery->where('appointments.outcome',$queryParams['outcome'] );
        }


        if(key_exists("franchise_id", $queryParams) && $queryParams['franchise_id'] !== ""){

            $mainQuery = $mainQuery->where('leads.franchise_id',$queryParams['franchise_id'] );
        }


        $mainQuery = $mainQuery->groupBy([
            'appointments.outcome',
            'franchises.franchise_number',
            'sales_staff.first_name',
            'sales_staff.last_name'
        ]);


        return $mainQuery->get();


    }

    public function generateOutcomeSalesStaffByFranchise($franchiseIds, $queryParams){

        $salesStaffQuery = DB::table('job_types')
            ->select(
                'job_types.lead_id',
                'sales_staff.first_name',
                'sales_staff.last_name'
            )->join('sales_staff', 'sales_staff.id', '=', 'job_types.sales_staff_id');


        $mainQuery = DB::table('leads')
            ->select(
                'appointments.outcome',
                'franchises.franchise_number'
            )
            ->selectRaw("concat(sales_staff.first_name, ' ', sales_staff.last_name) as salesStaff")
            ->selectRaw("count(leads.id) as numberOfLeads")
            ->join('appointments', 'appointments.lead_id', '=', 'leads.id')
            ->join('franchises', 'franchises.id', '=', 'leads.franchise_id')
            ->joinSub($salesStaffQuery, 'sales_staff', function ($join){
                $join->on('sales_staff.lead_id', '=', 'leads.id');
            })->whereIn('franchises.id', $franchiseIds);;


        if($queryParams['start_date'] !== null && $queryParams['end_date'] !== null){

            $mainQuery = $mainQuery
                ->whereBetween('leads.lead_date', [$queryParams['start_date'], $queryParams['end_date']]);
        }

        if(key_exists("outcome", $queryParams) && $queryParams['outcome'] !== ""){

            $mainQuery = $mainQuery->where('appointments.outcome',$queryParams['outcome'] );
        }


        if(key_exists("franchise_id", $queryParams) && $queryParams['franchise_id'] !== ""){

            $mainQuery = $mainQuery->where('leads.franchise_id',$queryParams['franchise_id'] );
        }


        $mainQuery = $mainQuery->groupBy([
            'appointments.outcome',
            'franchises.franchise_number',
            'sales_staff.first_name',
            'sales_staff.last_name'
        ]);


        return $mainQuery->get();



    }

}
