<?php 

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderPlacedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function build()
    {
        // Criteria: Laravel PDF for invoice generation 
        $pdf = Pdf::loadView('emails.invoice_pdf', ['order' => $this->order]);

        return $this->view('emails.order_placed')
            ->subject('Your Order Confirmation - ' . $this->order->order_number)
            ->attachData($pdf->output(), "invoice_{$this->order->order_number}.pdf", [
                'mime' => 'application/pdf',
            ]);
    }
}