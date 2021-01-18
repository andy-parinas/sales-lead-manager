<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\CustomerReviewReportInterface;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerReviewReportController extends ApiController
{

    protected $reportRepository;

    public function __construct(CustomerReviewReportInterface $reportRepository)
    {
        $this->middleware('auth:sanctum');
        $this->reportRepository = $reportRepository;
    }


    public function __invoke(Request $request)
    {
        $user = Auth::user();

        if($request->has('start_date') && $request->has('end_date')){

            $results = [];

            if($user->user_type == User::HEAD_OFFICE){

                $results = $this->reportRepository->getAll($request->all());

            }else {

                $franchiseIds = $user->franchises->pluck('id')->toArray();

                $results = $this->reportRepository->getAllByFranchise($franchiseIds, $request->all());
            }

            return $this->showOne([
                'results' => $results
            ]);
        }
    }
}
