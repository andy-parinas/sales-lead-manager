<?php


namespace App\Traits;

use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponser
{

    private function successResponse($data, $code)
    {
        return response()->json($data, $code);
    }


    protected function errorResponse($message, $code=Response::HTTP_INTERNAL_SERVER_ERROR)
    {
        return response()->json($message, $code);
    }

    protected function showAll(Collection $collection, $code=Response::HTTP_OK)
    {
        return $this->successResponse(['data' => $collection], $code);
    }

    protected function showOne($data, $code=Response::HTTP_OK)
    {

        return $this->successResponse(['data' => $data], $code);
    }




}