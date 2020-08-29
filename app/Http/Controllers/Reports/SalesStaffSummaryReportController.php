<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\ReportRepositoryInterface;
use App\Traits\ReportComputer;
use Illuminate\Http\Request;

class SalesStaffSummaryReportController extends ApiController
{

    use ReportComputer;

    protected $reportRepository;

    public function __construct(ReportRepositoryInterface $reportRepository)
    {
        $this->middleware('auth:sanctum');
        $this->reportRepository = $reportRepository;
    }

    public function index(Request $request)
    {

        if($request->has('start_date') && $request->has('end_date')){


            $results = $this->reportRepository->generateSalesSummary($request->all());

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

}
