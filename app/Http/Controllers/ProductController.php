<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    use ApiResponseTrait;

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
    public function store(StoreProductRequest $request): JsonResponse
    {
        try {
            $product = Product::create($request->validated());
            return $this->successResponse(
                ProductResource::make($product),
                'Product created successfully',
                201
            );
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): JsonResponse
    {
        return $this->successResponse(
            ProductResource::make($product),
            'Product retrieved successfully'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest  $request, Product $product): JsonResponse
    {
        try {
            $product->update($request->validated());
            return $this->successResponse(
                ProductResource::make($product),
                'Product updated successfully',
                200
            );
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): JsonResponse
    {
        try {
            $product->delete();
            return $this->successResponse(null, 'Product deleted successfully');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
    }
}
