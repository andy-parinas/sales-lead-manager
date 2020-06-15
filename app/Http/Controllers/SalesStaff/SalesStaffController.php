<?php

namespace App\Http\Controllers\SalesStaff;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\SalesStaffCollection;
use App\Repositories\Interfaces\SalesStafRepositoryInterface;
use App\SalesStaff;
use Illuminate\Http\Request;
use App\Http\Resources\SalesStaff as SalesStaffResource;
use Illuminate\Support\Facades\Gate;

class SalesStaffController extends ApiController
{
    private $salesStaffRepository;

    public function __construct(SalesStafRepositoryInterface $salesStaffRepository)
    {
        $this->middleware('auth:sanctum');
        $this->salesStaffRepository = $salesStaffRepository;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $salesStaffs = $this->salesStaffRepository->getAll($this->getRequestParams());


        return $this->showApiCollection(new SalesStaffCollection($salesStaffs));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Gate::authorize('head-office-only');

        $data = $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'contact_number' => 'required',
            'franchise_id' => 'required',
            'status'  => 'required'
        ]);

        $staff = SalesStaff::create($data);

        return $this->showOne(new SalesStaffResource($staff));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        Gate::authorize('head-office-only');

        $salesStaff = SalesStaff::findOrFail($id);

        $salesStaff->update($request->all());

        $salesStaff->refresh();


        return $this->showOne(new SalesStaffResource($salesStaff));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Gate::authorize('head-office-only');

        $staff = SalesStaff::findOrFail($id);

        $staff->delete();

        return $this->showOne($staff);
    }
}
