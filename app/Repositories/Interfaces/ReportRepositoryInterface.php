<?php


namespace App\Repositories\Interfaces;


interface ReportRepositoryInterface
{
    public function generateSalesSummary($queryParams);

    public function generateSalesSummaryForTest($start_date, $end_date);
}
