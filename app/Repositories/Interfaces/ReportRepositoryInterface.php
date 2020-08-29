<?php


namespace App\Repositories\Interfaces;


interface ReportRepositoryInterface
{
    public function generateSalesSummary($queryParams);

    public function generateProductSalesSummary($queryParams);

    public function generateSalesSummaryV2($queryParams);
}
