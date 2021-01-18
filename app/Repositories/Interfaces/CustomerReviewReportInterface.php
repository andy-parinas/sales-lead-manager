<?php


namespace App\Repositories\Interfaces;


interface CustomerReviewReportInterface
{

    public function getAll($queryParams);

    public function getAllByFranchise($franchiseIds, $queryParams);

}
