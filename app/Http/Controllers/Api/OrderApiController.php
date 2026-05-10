<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Services\OrderService;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\Auth;

class OrderApiController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    public function index()
    {
        $orders = Auth::user()->orders()->with('items.product')->latest()->get();
        return OrderResource::collection($orders);
    }

    public function store(OrderRequest $request)
    {
        // Transform items from [{product_id: 1, quantity: 2}] to [1 => {quantity: 2}]
        $cartItems = collect($request->input('items'))->mapWithKeys(function ($item) {
            return [$item['product_id'] => ['quantity' => $item['quantity']]];
        })->toArray();
        
        $order = $this->orderService->createOrder(
            $request->validated(), 
            $cartItems, 
            $request->input('branch_id')
        );
    
        return new OrderResource($order->load('items.product'));
    }
}