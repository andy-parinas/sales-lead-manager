<?php

namespace App\Http\Controllers\Postcode;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostcodeAllCollection;
use App\Http\Resources\PostcodeCollection;
use App\Postcode;
use App\Repositories\Interfaces\PostcodeRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PostcodeController extends ApiController
{

    private $postcodeRepository;

    public function __construct(PostcodeRepositoryInterface $postcodeRepository)
    {
        $this->middleware('auth:sanctum');
        $this->postcodeRepository = $postcodeRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $postcodes = $this->postcodeRepository->getAll($this->getRequestParams());

        if ($postcodes instanceof LengthAwarePaginator){
            return $this->showApiCollection(new PostcodeCollection($postcodes));
        }

        return $this->showApiCollection(new PostcodeAllCollection($postcodes));

    }

    public function search(Request $request)
    {
        $postcodes = [];

        if($request->has('search')){

            $postcodes = Postcode::where('pcode', 'LIKE', '%'.  $request['search'] .'%' )
                ->get()
                ->pluck('pcode');

        }

        return $this->showAll($postcodes);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
