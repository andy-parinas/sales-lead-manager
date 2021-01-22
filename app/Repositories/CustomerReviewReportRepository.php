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
                'sales_staff.id as sales_staff_id',
                'products.name as product_name',
                'franchises.franchise_number',
                'franchises.id as franchise_id',
                'franchises.type as franchise_type'

            )
            ->leftJoin('job_types', 'job_types.lead_id', '=',  'leads.id')
            ->leftJoin('sales_staff', 'job_types.sales_staff_id', '=',  'sales_staff.id')
            ->leftJoin('products', 'job_types.product_id', '=', 'products.id')
            ->leftJoin('franchises', 'franchises.id', '=', 'leads.franchise_id');


        $mainQuery = DB::table('customer_reviews')
            ->select(
                'customer_reviews.date_project_completed',
                'customer_reviews.date_warranty_received',
                'customer_reviews.workmanship_rating',
                'customer_reviews.service_received_rating',
                'customer_reviews.finished_product_rating',
                'customer_reviews.design_consultant_rating',
                'customer_reviews.comments',
                'leads.first_name', 'leads.last_name',
                'leads.product_name',
                'leads.lead_date',
                'leads.lead_number',
                'leads.franchise_number',
                'leads.franchise_id',
                'leads.franchise_type',
                'leads.sales_staff_id'
            )
            ->joinSub($subQuery, 'leads', function ($join){
                $join->on('leads.id', '=', 'customer_reviews.lead_id');
            });


        if($queryParams['start_date'] !== null && $queryParams['end_date'] !== null){

            $mainQuery = $mainQuery
                ->whereBetween('leads.lead_date', [$queryParams['start_date'], $queryParams['end_date']]);
        }


        if(key_exists("franchise_id", $queryParams) && $queryParams['franchise_id'] !== ""){

            $mainQuery = $mainQuery->where('leads.franchise_id',$queryParams['franchise_id'] );
        }


        if(key_exists("franchise_type", $queryParams) && $queryParams['franchise_type'] !== ""){

            $mainQuery = $mainQuery->where('leads.franchise_type', $queryParams['franchise_type'] );
        }

        if(key_exists("sales_staff_id", $queryParams) && $queryParams['sales_staff_id'] !== ""){

            $mainQuery = $mainQuery->where('leads.sales_staff_id',$queryParams['sales_staff_id'] );
        }


        if(key_exists("sort_by", $queryParams) && $queryParams['sort_by'] !== "" && key_exists("direction", $queryParams) && $queryParams['direction'] !== ""){

            $mainQuery = $mainQuery->orderBy($queryParams['sort_by'], $queryParams['direction']);
        }


        $mainQuery->groupBy([
            'customer_reviews.date_project_completed',
            'customer_reviews.date_warranty_received',
            'customer_reviews.workmanship_rating',
            'customer_reviews.service_received_rating',
            'customer_reviews.finished_product_rating',
            'customer_reviews.design_consultant_rating',
            'customer_reviews.comments',
            'leads.first_name', 'leads.last_name',
            'leads.lead_number',
            'leads.product_name',
            'leads.lead_date',
            'leads.franchise_number',
            'leads.franchise_id',
            'leads.franchise_type',
            'leads.sales_staff_id'
        ]);


        return $mainQuery->get();


    }

    public function getAllByFranchise($franchiseIds, $queryParams)
    {
        // TODO: Implement getAllByFranchise() method.
    }
}
