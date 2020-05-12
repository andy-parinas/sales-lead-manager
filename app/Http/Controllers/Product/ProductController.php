<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCollection;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends ApiController
{

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::all();

        return $this->showApiCollection(new ProductCollection($products));
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
           'name' => 'required|max:50',
           'description' => ''
        ]);

        $product = Product::create($data);

        return $this->showOne($product, Response::HTTP_CREATED);
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

        $product = Product::findOrFail($id);

        $updates = $this->validate($request, [
            'name' => 'max:50',
            'description' => ''
        ]);

        $product->update($updates);

        $product->refresh();

        return $this->showOne($product);

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

        $product = Product::findOrFail($id);

        $product->delete();

        return $this->showOne($product);

    }
}
