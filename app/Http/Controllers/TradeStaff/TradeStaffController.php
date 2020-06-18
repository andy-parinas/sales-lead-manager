<?php

namespace App\Http\Controllers\TradeStaff;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\TradeStaffCollection;
use App\Repositories\Interfaces\TradeStaffRepositoryInterface;
use App\TradeStaff;
use Illuminate\Http\Request;
use App\Http\Resources\TradeStaff as TradeStaffResource;

class TradeStaffController extends ApiController
{

    private $tradeStaffRepository;

    public function __construct(TradeStaffRepositoryInterface $tradeStaffRepository)
    {
        $this->middleware('auth:sanctum');
        $this->tradeStaffRepository = $tradeStaffRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $staffs = $this->tradeStaffRepository->getAll($this->getRequestParams());


        return $this->showApiCollection(new TradeStaffCollection($staffs));
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'contact_number' => 'required',
            'trade_type_id' => 'required',
            'company' => '',
            'abn' => '',
            'builders_license' => '',
            'status' => '',
            'franchise_id' => 'required',
        ]);

        $staff = TradeStaff::create($data);

        return $this->showOne(new TradeStaffResource($staff));


    }




    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $staff = TradeStaff::with(['franchise', 'tradeType'])->findOrFail($id);

        $data = $this->validate($request, [
            'first_name' => '',
            'last_name' => '',
            'email' => '',
            'contact_number' => '',
            'trade_type_id' => '',
            'company' => '',
            'abn' => '',
            'builders_license' => '',
            'status' => '',
            'franchise_id' => '',
        ]);

        $staff->update($data);

        $staff->refresh();

        return $this->showOne(new TradeStaffResource($staff));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $staff = TradeStaff::findOrFail($id);

        $staff->delete();

        return $this->showOne($staff);
    }
}
