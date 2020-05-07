<?php

namespace App\Repositories;

use App\Franchise;
use App\Repositories\Interfaces\LeadRepositoryInterface;
use Illuminate\Support\Facades\DB;

class LeadRepository implements LeadRepositoryInterface
{

    public function findSortPaginateByFranchise(Franchise $franchise, Array $params)
    {

        $query = DB::table('leads')->where('franchise_id', $franchise->id)
            ->join('sales_contacts', 'sales_contacts.id', '=', 'leads.sales_contact_id')
            ->join('lead_sources', 'lead_sources.id', '=', 'leads.lead_source_id')
            ->select(
                'leads.lead_number as leadNumber',
                'leads.lead_date',
                'leads.created_at as created_at',
                'lead_sources.name as source',
                'sales_contacts.id as salesContactId',
                'sales_contacts.first_name as firstName',
                'sales_contacts.last_name as lastName',
                'sales_contacts.email as email',
                'sales_contacts.contact_number as contactNumber',
                'sales_contacts.suburb',
                'sales_contacts.state',
                'sales_contacts.postcode'
            );

        if(key_exists('search', $params) && key_exists('on', $params))
        {
            return $query->where($params['on'], 'LIKE', '%' . $params['search'] . '%')
                    ->orderBy($params['column'], $params['direction'])
                    ->paginate($params['size']);

        }

        return $query->orderBy($params['column'], $params['direction'])
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
            ->select(
                'leads.id',
                    'leads.lead_number as leadNumber',
                    'leads.lead_date as leadDate',
                    'lead_sources.name as source',
                    'sales_contacts.id as salesContactId',
                    'sales_contacts.first_name as firstName',
                    'sales_contacts.last_name as lastName',
                    'sales_contacts.email',
                    'sales_contacts.postcode',
                    'franchises.franchise_number as franchiseNumber',
                    'job_types.id as job_type_id',
                    'job_types.taken_by as takenBy',
                    'job_types.date_allocated as dateAllocated',
                    'job_types.description as job_type_description as jobDescription',
                    'appointments.id as appointment_id',
                    'appointments.appointment_date as appointmentDate ',
                    'appointments.appointment_notes as appointmentNotes',
                    'appointments.quoted_price as quotedPrice',
                    'appointments.outcome',
                    'appointments.comments',
                    'products.id as product_id',
                    'products.name as product_name as productName',
                    'design_assessors.id as design_assessor_id',
                    'design_assessors.first_name as design_assessors_first_name as designAssessorFirstName',
                    'design_assessors.last_name as design_assessors_last_name as designAssessorLastName')
            ->first();
    }


    public function findLeadsByUsersFranchise(Array $franchiseIds, Array $params)
    {

        $query = DB::table('leads')->whereIn('franchise_id', $franchiseIds)
            ->join('franchises', 'franchises.id', '=', 'leads.franchise_id')
            ->join('sales_contacts', 'sales_contacts.id', '=', 'leads.sales_contact_id')
            ->join('lead_sources', 'lead_sources.id', '=', 'leads.lead_source_id')
//            ->leftJoin('job_types', function($join) {
//                $join->on( 'job_types.lead_id', '=', 'leads.id')
//                    ->join('products', 'products.id', '=', 'job_types.product_id')
//                    ->join('design_assessors', 'design_assessors.id', '=', 'job_types.design_assessor_id');
//            })
            ->leftJoin('appointments', 'appointments.lead_id', '=', 'leads.id')
            ->select(
                'leads.id as leadId',
                'leads.lead_number as leadNumber',
                'franchises.franchise_number as franchiseNumber',
                'leads.lead_date as leadDate',
                'leads.created_at as created_at',
                'lead_sources.name as source',
                'sales_contacts.first_name as firstName',
                'sales_contacts.last_name as lastName',
                'sales_contacts.email as email',
                'sales_contacts.contact_number as contactNumber',
                'sales_contacts.suburb',
                'sales_contacts.state',
                'sales_contacts.postcode',
                'appointments.outcome as outcome'
            );

        if(key_exists('search', $params) && key_exists('on', $params))
        {

            return  $query->where($params['on'], 'LIKE', '%' . $params['search'] . '%')
                        ->orderBy($params['column'], $params['direction'])
                        ->paginate($params['size']);

        }

        return $query->orderBy($params['column'], $params['direction'])
                    ->paginate($params['size']);

    }

    public function getAllLeads(Array $params)
    {
        $query = DB::table('leads')
            ->join('franchises', 'franchises.id', '=', 'leads.franchise_id')
            ->join('sales_contacts', 'sales_contacts.id', '=', 'leads.sales_contact_id')
            ->join('lead_sources', 'lead_sources.id', '=', 'leads.lead_source_id')
//            ->leftJoin('job_types', function($join) {
//                $join->on( 'job_types.lead_id', '=', 'leads.id')
//                    ->join('products', 'products.id', '=', 'job_types.product_id')
//                    ->join('design_assessors', 'design_assessors.id', '=', 'job_types.design_assessor_id');
//            })
            ->leftJoin('appointments', 'appointments.lead_id', '=', 'leads.id')
            ->select(
                'leads.id as leadId',
                'leads.lead_number as leadNumber',
                'franchises.franchise_number as franchiseNumber',
                'leads.lead_date as leadDate',
                'leads.created_at as created_at',
                'lead_sources.name as source',
                'sales_contacts.first_name as firstName',
                'sales_contacts.last_name as lastName',
                'sales_contacts.email as email',
                'sales_contacts.contact_number as contactNumber',
                'sales_contacts.suburb',
                'sales_contacts.state',
                'sales_contacts.postcode',
                'appointments.outcome as outcome'
            );

        if(key_exists('search', $params) && key_exists('on', $params))
        {

            return  $query->where($params['on'], 'LIKE', '%' . $params['search'] . '%')
                        ->orderBy($params['column'], $params['direction'])
                        ->paginate($params['size']);

        }

        return $query->orderBy($params['column'], $params['direction'])
                    ->paginate($params['size']);
    }
}
