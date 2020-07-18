<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\ReportRepositoryInterface;
use Illuminate\Http\Request;

class SalesSummaryReportController extends ApiController
{
    protected $reportRepostitory;

    public function __construct(ReportRepositoryInterface $reportRepository)
    {
        $this->middleware('auth:sanctum');
        $this->reportRepostitory = $reportRepository;
    }

    public function index(Request $request)
    {
        if($request->has('start_date') && $request->has('end_date')){

            $results = $this->reportRepostitory->generateSalesSummary($request->start_date, $request->end_date);

            return $this->showAll($results);

        }
    }

    private function computeTotal($results)
    {
            $totalNumberOfSales = 0;
            $totalNumberOfLeads = 0;
            $totalConversionRate = 0;
            $grandTotalContracts = 0;
            $grandAveragePrice = 0;

            foreach ($results as $result){

            }
    }

}
