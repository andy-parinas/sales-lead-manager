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


            $results = $this->reportRepostitory->generateSalesSummary($request->all());
            //$results = $this->reportRepostitory->generateSalesSummaryForTest($request->start_date, $request->end_date);

            if($results->count() > 0){
                $total = $this->computeTotal($results);

                return $this->showOne([
                    'results' => $results,
                    'total' => $total
                ]);

            }

            return $this->showOne([
                'results' => $results
            ]);
        }
    }

    private function computeTotal($results)
    {
            $totalNumberOfSales = 0;
            $totalNumberOfLeads = 0;
            $totalConversionRate = 0;
            $grandTotalContracts = 0;
            $grandTotalAveragePrice = 0;


            foreach ($results as $result){
                $totalNumberOfSales = $totalNumberOfSales + $result->numberOfSales;
                $totalNumberOfLeads = $totalNumberOfLeads + $result->numberOfLeads;
                $totalConversionRate = $totalConversionRate + $result->conversionRate;
                $grandTotalContracts = $grandTotalContracts + $result->totalContracts;
                $grandTotalAveragePrice = $grandTotalAveragePrice + $result->averageSalesPrice;
            }

            $resultLength = count($results);

            return [
                'totalNumberOfSales' => $totalNumberOfSales,
                'totalNumberOfLeads' => $totalNumberOfLeads,
                'averageConversionRate' => $totalConversionRate / $resultLength,
                'grandTotalContracts' => $grandTotalContracts,
                'grandAveragePrice' => $grandTotalAveragePrice / $resultLength,
                'averageNumberOfLeads' => $totalNumberOfLeads / $resultLength,
                'averageNumberOfSales' => $totalNumberOfSales / $resultLength,
                'averageTotalContract' => $grandTotalContracts / $resultLength,
            ];
    }

}
