<?php

namespace App\Repositories\Interfaces;

use App\Franchise;

interface LeadRepositoryInterface
{

    public function findSortPaginateByFranchise(Franchise $franchise, Array $params);

    public function findLeadById($lead_id);

    public function findLeadsByUsersFranchise(Array $franchiseIds, Array $params);

    public function getAllLeads(Array $params);

}
