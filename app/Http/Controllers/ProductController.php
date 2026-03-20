<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $cacheKey = 'products:' . md5(json_encode($request->all()));

        $products = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($request) {
            return ProductResource::collection(
                Product::query()
                    ->when($request->name, fn($q) => $q->where('name', 'like', "%{$request->name}%"))
                    ->when($request->min_price, fn($q) => $q->where('price', '>=', $request->min_price))
                    ->when($request->max_price, fn($q) => $q->where('price', '<=', $request->max_price))
                    ->when($request->stock_quantity, fn($q) => $q->where('stock_quantity', '>=', $request->stock_quantity))
                    ->get()
            )->resolve();
        });

        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request): ProductResource
    {
        return ProductResource::make(Product::create($request->validated()));
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): ProductResource
    {
        return ProductResource::make($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product): ProductResource
    {
        $product->update($request->all());
        return ProductResource::make($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();
        return response()->json(null, 204);
    }
}
