<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Xendit\Xendit;

class PaymentController extends Controller
{
    public function payNow(Request $request)
    {
        Xendit::setApiKey(config('services.xendit.secret_key'));

        // Total yang akan dibayar
        $amount = $request->total; // pastikan kamu kirim ini dari checkout

        // Buat Invoice Xendit
        $invoice = \Xendit\Invoice::create([
            'external_id' => 'order-' . time(),
            'amount' => $amount,
            'description' => 'Pembayaran Checkout',
            'success_redirect_url' => route('checkout.success'),
            'failure_redirect_url' => route('checkout.failed')
        ]);

        return redirect($invoice['invoice_url']);
    }
}
