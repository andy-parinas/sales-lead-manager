<?php

namespace App\Repositories;

use App\Franchise;
use App\Repositories\Interfaces\LeadRepositoryInterface;
use Illuminate\Support\Facades\DB;

class LeadRepository implements LeadRepositoryInterface
{

    public function findSortPaginateByFranchise(Franchise $franchise, Array $params)
    {   


        return DB::table('leads')->where('franchise_id', $franchise->id)
            ->join('sales_contacts', 'sales_contacts.id', '=', 'leads.sales_contact_id')
            ->join('lead_sources', 'lead_sources.id', '=', 'leads.lead_source_id')
            ->select('leads.number', 'leads.lead_date', 'lead_sources.name as source', 'sales_contacts.*')
            ->orderBy($params['column'], $params['direction'])
            ->paginate($params['size']);


    }
}