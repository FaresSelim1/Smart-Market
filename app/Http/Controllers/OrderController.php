<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Controller handling order listing, detail views, and invoice downloads.
 */
class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    /**
     * Display the authenticated user's order history.
     */
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with('branch')
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Display a single order's details.
     */
    public function show(Order $order)
    {
        // Security check: ensure user owns the order
        abort_if($order->user_id !== Auth::id(), 403);

        // Load relationships for the view
        $order->load(['items.product', 'branch']);

        return view('orders.show', compact('order'));
    }

    /**
     * Download the invoice PDF for an order.
     */
    public function downloadInvoice(Order $order)
    {
        abort_if($order->user_id !== Auth::id(), 403);

        $order->load(['items.product', 'branch', 'user']);

        $pdf = Pdf::loadView('emails.invoice_pdf', ['order' => $order]);

        return $pdf->download("invoice_{$order->order_number}.pdf");
    }
}