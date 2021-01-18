<?php


namespace App\Repositories;


use App\Repositories\Interfaces\CustomerReviewReportInterface;
use Illuminate\Support\Facades\DB;

class CustomerReviewReportRepository implements CustomerReviewReportInterface
{


    public function getAll($queryParams)
    {
        $subQuery = DB::table('leads')
            ->select('leads.id',
                'leads.lead_number',
                'leads.lead_date',
                'sales_staff.first_name',
                'sales_staff.last_name',
                'products.name as product_name',
                'franchises.franchise_number')
            ->leftJoin('job_types', 'job_types.lead_id', '=',  'leads.id')
            ->leftJoin('sales_staff', 'job_types.sales_staff_id', '=',  'sales_staff.id')
            ->leftJoin('products', 'job_types.product_id', '=', 'products.id')
            ->leftJoin('franchises', 'franchises.id', '=', 'leads.franchise_id');


        $mainQuery = DB::table('customer_reviews')
            ->select('customer_reviews.date_project_completed',
                'customer_reviews.workmanship_rating',
                'leads.first_name', 'leads.last_name',
                'leads.product_name',
                'leads.lead_date',
                'leads.lead_number', 'leads.franchise_number')
            ->joinSub($subQuery, 'leads', function ($join){
                $join->on('leads.id', '=', 'customer_reviews.lead_id');
            });


        if($queryParams['start_date'] !== null && $queryParams['end_date'] !== null){

            $mainQuery = $mainQuery
                ->whereBetween('leads.lead_date', [$queryParams['start_date'], $queryParams['end_date']]);
        }


        $mainQuery->groupBy([
            'customer_reviews.date_project_completed',
            'customer_reviews.workmanship_rating',
            'leads.first_name', 'leads.last_name',
            'leads.lead_number',
            'leads.product_name',
            'leads.lead_date',
            'leads.franchise_number'
        ]);


        return $mainQuery->get();


    }

    public function getAllByFranchise($franchiseIds, $queryParams)
    {
        // TODO: Implement getAllByFranchise() method.
    }
}
