<?php


namespace App\Repositories\Interfaces;


interface ReportRepositoryInterface
{
    public function generateSalesSummary($start_date, $end_date);
}
