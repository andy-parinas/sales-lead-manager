<?php


namespace App\Repositories\Interfaces;


interface ReportRepositoryInterface
{
    public function generateSalesSummary($queryParams);

    public function generateSalesSummaryByFranchises($franchiseIds, $queryParams);

    public function generateSalesStaffProductSummary($queryParams);

    public function generateSalesStaffProductSummaryByFranchises($franchiseIds, $queryParams);

    public function generateProductSalesSummary($queryParams);

    public function generateSalesSummaryV2($queryParams);

    public function generateOutcomeSalesStaff($queryParams);

    public function generateOutcomeSalesStaffByFranchise($franchiseIds, $queryParams);

    public function generateOutcome($queryParams);

    public function generateOutcomeByFranchise($franchiseIds, $queryParams);

    public function generateLeadSource($queryParams);

    public function generateLeadSourceByFranchise($franchiseIds, $queryParams);
}
