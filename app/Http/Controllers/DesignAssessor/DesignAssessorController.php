<?php

namespace App\Http\Controllers\DesignAssessor;

use App\DesignAssessor;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\DesignAssessorCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class DesignAssessorController extends ApiController
{

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }


    public function store(Request $request)
    {

        Gate::authorize('head-office-only');

        $data = $this->validate($request, [
           'first_name' => 'required',
           'last_name' => 'required',
           'email' => 'required|email',
           'contact_number' => 'required'
        ]);

        $designAssessor = DesignAssessor::create($data);

        return $this->showOne($designAssessor, Response::HTTP_CREATED);
    }


    public function index(Request $request)
    {

        if ($request->has('search')){



            $assessors = DesignAssessor::where('first_name', 'like',  $request['search'] . '%')
                                    ->orWhere('last_name', 'like',  $request['search'] . '%')
                                    ->orWhere('email', 'like', '%' . $request['search'] . '%')
                                    ->get();

            return $this->showApiCollection(new DesignAssessorCollection($assessors));

        }


        $assessors = DesignAssessor::all();

        return $this->showApiCollection(new DesignAssessorCollection($assessors));

    }



}
