<?php

namespace App\Http\Controllers;

use App\Services\XenditService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $xendit;

    public function __construct(XenditService $xendit)
    {
        $this->xendit = $xendit;
    }

    public function createInvoice(Request $request)
    {
        $params = [
            "external_id" => "invoice-" . time(),
            "payer_email" => "customer@mail.com",
            "description" => "Pembayaran Order",
            "amount" => 150000,
            "success_redirect_url" => url('/payment/success'),
            "failure_redirect_url" => url('/payment/failed')
        ];

        $invoice = $this->xendit->createInvoice($params);

        return redirect($invoice['invoice_url']);
    }

    public function callback(Request $request)
    {
        // biasanya data dari Xendit dikirim ke webhook
        $data = $request->all();

        // contoh update status order
        // Order::where('external_id', $data['external_id'])->update([
        //     'status' => $data['status']
        // ]);

        return response()->json(['success' => true]);
    }
}
