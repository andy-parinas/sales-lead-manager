<?php

namespace App\Repositories;

use App\Franchise;
use App\Repositories\Interfaces\LeadRepositoryInterface;
use Illuminate\Support\Facades\DB;

class LeadRepository implements LeadRepositoryInterface
{

    public function findSortPaginateByFranchise(Franchise $franchise, Array $params)
    {   

        if(key_exists('search', $params) && key_exists('on', $params))
        {

            return  DB::table('leads')->where('franchise_id', $franchise->id)
                ->join('sales_contacts', 'sales_contacts.id', '=', 'leads.sales_contact_id')
                ->join('lead_sources', 'lead_sources.id', '=', 'leads.lead_source_id')
                ->select('leads.number', 'leads.lead_date', 'lead_sources.name as source', 'sales_contacts.*')
                ->where($params['on'], 'LIKE', '%' . $params['search'] . '%')
                ->orderBy($params['column'], $params['direction'])
                ->paginate($params['size']);

        }

        return DB::table('leads')->where('franchise_id', $franchise->id)
            ->join('sales_contacts', 'sales_contacts.id', '=', 'leads.sales_contact_id')
            ->join('lead_sources', 'lead_sources.id', '=', 'leads.lead_source_id')
            ->select('leads.number', 'leads.lead_date', 'lead_sources.name as source', 'sales_contacts.*')
            ->orderBy($params['column'], $params['direction'])
            ->paginate($params['size']);


    }

    public function findLeadById($lead_id)
    {
        return DB::table('leads')->where('leads.id', $lead_id)
            ->join('franchises', 'franchises.id', '=', 'leads.franchise_id')
            ->join('sales_contacts', 'sales_contacts.id', '=', 'leads.sales_contact_id')
            ->join('lead_sources', 'lead_sources.id', '=', 'leads.lead_source_id')
            ->leftJoin('job_types', function($join) use ($lead_id) {
                $join->on( 'job_types.lead_id', '=', $lead_id)
                ->join('products', 'products.id', '=', 'job_types.product_id')
                ->join('design_assessors', 'design_assessors.id', '=', 'job_types.design_assessor_id');
            })
            ->leftJoin('appointments', 'appointments.lead_id', '=', $lead_id)
            ->select('leads.id', 'leads.number', 'leads.lead_date', 'lead_sources.name as source', 
                    'sales_contacts.first_name', 
                    'sales_contacts.last_name',
                    'sales_contacts.email',
                    'sales_contacts.postcode',
                    'franchises.number as franchise_number', 
                    'job_types.id as job_type_id', 
                    'job_types.taken_by', 
                    'job_types.date_allocated',
                    'job_types.description as job_type_description',
                    'appointments.id as appointment_id',
                    'appointments.appointment_date',
                    'appointments.appointment_notes',
                    'appointments.quoted_price',
                    'appointments.outcome',
                    'appointments.comments',
                    'products.id as product_id',
                    'products.name as product_name',
                    'design_assessors.id as design_assessor_id',
                    'design_assessors.first_name as design_assessors_first_name',
                    'design_assessors.last_name as design_assessors_last_name')
            ->first();
    }
}