<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    public function __construct(
        protected ProductRepositoryInterface $productRepo
    ) {}

    public function index(Request $request)
    {
        // Uses the branch_id from the mobile app header
        $branchId = $request->header('X-Branch-Id');
        
        $products = $this->productRepo->getBranchProducts($branchId);

        return ProductResource::collection($products);
    }
}