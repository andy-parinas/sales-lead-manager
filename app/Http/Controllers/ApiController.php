<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ApiController extends Controller
{
    use ApiResponser;


    protected function getRequestParams()
    {
        
        $column = request()->has('sortBy') ? request()->sortBy : 'number';
        $direction = request()->has('direction') ? request()->direction : 'asc';
        $size = request()->has('size') ? request()->size : 15;

        if(request()->has('search') && request()->has('on')){

            return [
                'column' => $column,
                'direction' => $direction,
                'size' => $size,
                'search' => request()->search,
                'on' => request()->on
            ];
        }

        return [
            'column' => $column,
            'direction' => $direction,
            'size' =>$size
        ];
    }

    public function isAllow($ability)
    {
        if(Gate::denies($ability)){
            // return $this->errorResponse("Not authorized to create user", Response::HTTP_FORBIDDEN);
            throw new AuthorizationException();
        }
    }

}
