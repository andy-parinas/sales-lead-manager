<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\ReportRepositoryInterface;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OutcomeSummaryReportController extends ApiController
{

    protected $reportRepository;

    public function __construct(ReportRepositoryInterface $reportRepository)
    {
        $this->reportRepository = $reportRepository;
        $this->middleware('auth:sanctum');
    }


    public function __invoke(Request $request)
    {
        $user = Auth::user();

        if($request->has('start_date') && $request->has('end_date')){

            $results = [];

            if($user->user_type == User::HEAD_OFFICE){

                $results = $this->reportRepository->generateOutcome($request->all());

            }else {

                $franchiseIds = $user->franchises->pluck('id')->toArray();

                $results = $this->reportRepository->generateOutcomeByFranchise($franchiseIds, $request->all());
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



        private function computeTotal($results)
    {
            $totalOutcome = 0;


            foreach ($results as $result){
                $totalOutcome = $totalOutcome + $result->numberOfLeads;
            }


            return [
                'totalOutcome' => $totalOutcome,
            ];
    }

}
