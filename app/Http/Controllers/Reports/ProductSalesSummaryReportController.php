<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\ReportRepositoryInterface;
use Illuminate\Http\Request;

class ProductSalesSummaryReportController extends Controller
{

    protected $reportRepository;

    public function __construct(ReportRepositoryInterface $reportRepository)
    {
        $this->middleware('auth:sanctum');
        $this->reportRepository = $reportRepository;
    }


    public function index(Request $request)
    {
        $results = $this->reportRepository->generateProductSalesSummary($request->all());

        return $results;

    }



}
