<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\ReportRepositoryInterface;
use App\Traits\ReportComputer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesSummaryReportController extends ApiController
{

    use ReportComputer;

    protected $reportRepostitory;

    public function __construct(ReportRepositoryInterface $reportRepository)
    {
        $this->middleware('auth:sanctum');
        $this->reportRepostitory = $reportRepository;
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        if($request->has('start_date') && $request->has('end_date')){

            $results = [];

            if($user->user_type == User::HEAD_OFFICE){
                $results = $this->reportRepostitory->generateSalesStaffProductSummary($request->all());
            }else {
                $franchiseIds = $user->franchises->pluck('id')->toArray();
                
                $results = $this->reportRepository->generateSalesSummaryByFranchises($franchiseIds, $request->all());
            }


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

//    private function computeTotal($results)
//    {
//            $totalNumberOfSales = 0;
//            $totalNumberOfLeads = 0;
//            $totalConversionRate = 0;
//            $grandTotalContracts = 0;
//            $grandTotalAveragePrice = 0;
//
//
//            foreach ($results as $result){
//                $totalNumberOfSales = $totalNumberOfSales + $result->numberOfSales;
//                $totalNumberOfLeads = $totalNumberOfLeads + $result->numberOfLeads;
//                $totalConversionRate = $totalConversionRate + $result->conversionRate;
//                $grandTotalContracts = $grandTotalContracts + $result->totalContracts;
//                $grandTotalAveragePrice = $grandTotalAveragePrice + $result->averageSalesPrice;
//            }
//
//            $resultLength = count($results);
//
//            return [
//                'totalNumberOfSales' => $totalNumberOfSales,
//                'totalNumberOfLeads' => $totalNumberOfLeads,
//                'averageConversionRate' => $totalConversionRate / $resultLength,
//                'grandTotalContracts' => $grandTotalContracts,
//                'grandAveragePrice' => $grandTotalAveragePrice / $resultLength,
//                'averageNumberOfLeads' => $totalNumberOfLeads / $resultLength,
//                'averageNumberOfSales' => $totalNumberOfSales / $resultLength,
//                'averageTotalContract' => $grandTotalContracts / $resultLength,
//            ];
//    }

}
