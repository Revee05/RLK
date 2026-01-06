<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Xendit\Xendit;
use Xendit\Invoice;
use App\OrderMerch;
use App\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use App\Models\MerchProductVariant;
use App\Models\MerchProductVariantSize;
use App\Mail\OrderConfirmationMail;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    public function payXendit(Request $request, $invoice)
    {
        Log::info('payXendit dipanggil', [
            'invoice_param' => $invoice,
            'request_data' => $request->all(),
            'user_id' => auth()->id(),
        ]);

        // Try OrderMerch first
        $order = OrderMerch::where('invoice', $invoice)->first();
        $orderType = 'merch';

        // If not found, try Order (lelang)
        if (!$order) {
            $order = Order::where('invoice', $invoice)->first();
            $orderType = 'lelang';
        }

        if (!$order) {
            abort(404, 'Order tidak ditemukan');
        }

        Log::info('Order ditemukan', [
            'order_id' => $order->id,
            'type' => $orderType
        ]);

        // Check status (unified field atau fallback ke payment_status untuk Order lama)
        $currentStatus = $order->status ?? ($order->payment_status == 1 ? 'pending' : 'success');
        
        if ($currentStatus !== 'pending') {
            return redirect()
                ->route('payment.status', $invoice)
                ->with('error', 'Pesanan tidak dapat dibayar.');
        }

        try {
            // Set API key untuk Xendit PHP SDK v2.x
            \Xendit\Xendit::setApiKey(env('XENDIT_SECRET_KEY'));

            Log::info('Xendit API key diset');

            $params = [
                'external_id' => $order->invoice,
                'amount' => $order->total_tagihan,
                'payer_email' => auth()->user()->email,
                'description' => 'Pembayaran ' . $order->invoice,
                'currency' => 'IDR',
                'invoice_duration' => 60, //1 jam
                'success_redirect_url' => url('/payment/status/' . $order->invoice),
                'failure_redirect_url' => url('/payment/status/' . $order->invoice),
                'customer_notification_preference' => [
                    'invoice_created' => ['email'],
                    'invoice_reminder' => ['email'],
                    'invoice_paid' => ['email'],
                ],
            ];

            // v2.x: use static Invoice class
            $xenditInvoice = \Xendit\Invoice::create($params);

            $invoiceUrl = null;
            if (is_array($xenditInvoice)) {
                $invoiceUrl = $xenditInvoice['invoice_url'] ?? null;
            } elseif (is_object($xenditInvoice)) {
                $invoiceUrl = $xenditInvoice->invoice_url ?? null;
            }

            Log::info('Invoice berhasil dibuat', [
                'invoice_url' => $invoiceUrl
            ]);

            $order->update([
                'payment_url' => $invoiceUrl,
                'status' => 'pending',
            ]);
            
            // Update payment_status untuk Order lama (backward compat)
            if ($orderType === 'lelang' && method_exists($order, 'setAttribute')) {
                $order->setAttribute('payment_status', 1);
                $order->save();
            }

            return redirect($invoiceUrl ?: url('/payment/status/' . $order->invoice));

        } catch (\Exception $e) {
            Log::error('Gagal membuat invoice', ['message' => $e->getMessage()]);
            return back()->with('error', 'Gagal membuat invoice: ' . $e->getMessage());
        }

    }

    public function handle(Request $request)
    {
        Log::info("Xendit Callback Masuk", $request->all());

        Log::info('WEBHOOK MASUK TOTAL', [
            'headers' => $request->headers->all(),
            'body' => $request->all()
        ]);


        // Validasi token
        $callbackToken = $request->header('x-callback-token');

        if ($callbackToken !== env('XENDIT_CALLBACK_TOKEN')) {
            Log::error("Token invalid");
            return response()->json(['message' => 'Invalid token'], 403);
        }

        // Xendit payload bisa datanya ada di "data" atau langsung di body
        $body = $request->input('data', $request->all());

        $externalId = $body['external_id'] ?? null;
        $status     = $body['status'] ?? null;

        if (!$externalId) {
            Log::error("Missing external_id");
            return response()->json(['message' => 'Missing external_id'], 400);
        }

        // Cari order berdasarkan external_id
        $order = OrderMerch::where('invoice', $externalId)->first();
        $orderType = 'merch';

        if (!$order) {
            $order = Order::where('invoice', $externalId)->first();
            $orderType = 'lelang';
        }

        if (!$order) {
            Log::error("Order tidak ditemukan: " . $externalId);
            return response()->json(['message' => 'Order not found'], 404);
        }

        Log::info("Order ditemukan untuk webhook", [
            'invoice' => $externalId,
            'type' => $orderType,
            'status_xendit' => $status
        ]);

        switch ($status) {
            case 'PAID':
                $order->update([
                    'status'  => 'success',
                    'paid_at' => now(),
                ]);
                // Update payment_status untuk Order lama
                if ($orderType === 'lelang') {
                    $order->setAttribute('payment_status', 2);
                    $order->save();
                }
                break;
            case 'EXPIRED':
                $order->update(['status' => 'expired']);
                if ($orderType === 'lelang') {
                    $order->setAttribute('payment_status', 3);
                    $order->save();
                }
                break;
            case 'FAILED':
                $order->update(['status' => 'cancelled']);
                if ($orderType === 'lelang') {
                    $order->setAttribute('payment_status', 4);
                    $order->save();
                }
                break;
            default:
                $order->update([
                    'status' => 'pending'
                ]);
                if ($orderType === 'lelang') {
                    $order->setAttribute('payment_status', 1);
                    $order->save();
                }
                break;
        }

        // SIMPAN METODE PEMBAYARAN DARI XENDIT
        $updateData['payment_method']     = $body['payment_method']     ?? $order->payment_method;
        $updateData['payment_channel']    = $body['payment_channel']    ?? $order->payment_channel;
        $updateData['payment_destination'] = $body['payment_destination'] ?? $order->payment_destination;
    
        $order->update($updateData);

        //$order->save();

        // =======================================================
        //   PENGURANGAN STOK — HANYA KALAU STATUS = PAID
        // =======================================================
        if ($status === 'PAID' && $orderType === 'merch') {

            // Idempotency → kalau sudah pernah dapat PAID, jangan kurangi stok lagi
            if ($order->stock_reduced == 1) {
                Log::info("Stok sudah pernah dikurangi, SKIP", ['invoice' => $order->invoice]);
                return response()->json(['message' => 'OK'], 200);
            }

            $items = json_decode($order->items, true) ?? [];

            DB::beginTransaction();
            try {

                foreach ($items as $item) {
                    $qty = $item['qty'] ?? 1;

                    // =========================
                    // JIKA ADA SIZE
                    // =========================
                    if (!empty($item['size_id'])) {

                        $size = MerchProductVariantSize::where(
                            'id',
                            $item['size_id']
                        )->lockForUpdate()->first();

                        if (!$size || $size->stock < $qty) {
                            throw new \Exception('Stok size tidak cukup');
                        }

                        $size->decrement('stock', $qty);

                        Log::info('Stok SIZE dikurangi', [
                            'size_id' => $item['size_id'],
                            'qty' => $qty
                        ]);
                    } 
                    // =========================
                    // VARIANT (TANPA SIZE)
                    // =========================
                    elseif (!empty($item['variant_id'])) {

                        $variant = MerchProductVariant::where(
                            'id',
                            $item['variant_id']
                        )->lockForUpdate()->first();

                        if (!$variant || $variant->stock < $qty) {
                            throw new \Exception('Stok variant tidak cukup');
                        }

                        $variant->decrement('stock', $qty);

                        Log::info('Stok VARIANT dikurangi', [
                            'variant_id' => $item['variant_id'],
                            'qty' => $qty
                        ]);
                    }
                }


                // Tandai stok sudah dikurangi (biar idempotent)
                $order->stock_reduced = 1;
                $order->save();

                DB::commit();

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Gagal mengurangi stok", ['message' => $e->getMessage()]);
            }
        }
        
        // ========================
        //   LOG TRANSAKSI GAGAL
        // ========================
        if (in_array($status, ['EXPIRED', 'FAILED'])) {
            Log::info("Transaksi tidak berhasil", [
                'invoice' => $order->invoice,
                'mapped_status' => $order->status
            ]);
        }

        // =======================================================
        //   KIRIM EMAIL KONFIRMASI — HANYA KALAU STATUS = PAID
        // =======================================================
        if ($status === 'PAID') {
            $emailSent = $orderType === 'lelang' 
                ? ($order->email_sent ?? 0) 
                : $order->email_sent;
                
            if ($emailSent == 1) {
                Log::info("Email sudah pernah dikirim, SKIP", ['invoice' => $order->invoice]);
            } else {
                try {
                    \Mail::to($order->email)->send(new \App\Mail\OrderConfirmationMail($order));
                    
                    // Update flag email_sent
                    $order->update(['email_sent' => 1]);
                    
                    Log::info("Email konfirmasi berhasil dikirim", [
                        'invoice' => $order->invoice,
                        'email' => $order->email,
                        'order_type' => $orderType
                    ]);
                } catch (\Exception $e) {
                    Log::error("Gagal mengirim email konfirmasi", [
                        'invoice' => $order->invoice,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        Log::info("Order Updated dari Callback", [
            'invoice' => $externalId,
            'status'  => $status,
            'payment_method' => $updateData['payment_method'] ?? null,
            'payment_channel' => $updateData['payment_channel'] ?? null,
        ]);

        return response()->json(['message' => 'OK'], 200);
    }

    public function status($invoice)
    {
        // Try OrderMerch first
        $order = OrderMerch::where('invoice', $invoice)->first();
        $orderType = 'merch';

        if (!$order) {
            $order = Order::where('invoice', $invoice)->first();
            $orderType = 'lelang';
        }

        if (!$order) {
            abort(404, 'Order tidak ditemukan');
        }

        Log::info("Halaman success dipanggil", [
            'invoice' => $invoice,
            'current_status' => $order->status ?? $order->payment_status,
            'type' => $orderType
        ]);

        // Jika sudah PAID dari webhook → tidak perlu cek Xendit lagi
        $currentStatus = $order->status ?? ($order->payment_status == 2 ? 'success' : 'pending');
        
        if ($currentStatus === 'success') {
            Log::info("Order sudah PAID — SKIP cek Xendit, langsung render success page");
            if (empty($order->email_sent)) {
                if ($order->user && $order->user->email) {
                    try {
                        Mail::to($order->user->email)
                            ->send(new OrderConfirmationMail($order));

                        Log::info('Email konfirmasi berhasil dikirim via status page', ['invoice' => $order->invoice]);

                        // Tandai email sudah dikirim (opsional)
                        $order->update(['email_sent' => 1]);
                    } catch (\Exception $e) {
                        Log::error('Gagal mengirim email konfirmasi via status page', [
                            'invoice' => $order->invoice,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
            return view('web.payment.status', compact('order'));
        }
        
        try {
            $config = Configuration::getDefaultConfiguration()
                ->setApiKey(env('XENDIT_SECRET_KEY'));

            $api = new InvoiceApi(new \GuzzleHttp\Client(), $config);

            // Ambil invoice berdasarkan external_id
            $invoiceList = $api->getInvoices("external_id=" . $invoice);
            $invoiceData = $invoiceList[0] ?? null;

            if ($invoiceData) {

                $status = $invoiceData->getStatus() ?? null;


                Log::info("Status invoice dari Xendit berhasil diambil", [
                    'external_id' => $invoice,
                    'status' => $status
                ]);

                switch ($status) {
                    case 'PAID':
                        $order->update([
                            'status' => 'success',
                            'paid_at' => now(),
                        ]);

                        Log::info("BERHASIL update status order → PAID", [
                            'invoice' => $invoice,
                            'paid_at' => now()->toDateTimeString()
                        ]);
                        break;

                    case 'EXPIRED':
                        $order->update(['status' => 'expired']);

                        Log::info("BERHASIL update status order → EXPIRED", [
                            'invoice' => $invoice,
                        ]);
                        break;

                    case 'FAILED':
                        $order->update(['status' => 'cancelled']);

                        Log::info("BERHASIL update status order → FAILED", [
                            'invoice' => $invoice,
                        ]);
                        break;

                    default:
                        $order->update(['status' => 'pending']);
                        
                        Log::info("BERHASIL update status order → MENUNGGU PEMBAYARAN", [
                            'invoice' => $invoice,
                        ]);
                        break;
                }
                // Update payment method
                $update['payment_method'] = $invoiceData->getPaymentMethod() ?? $order->payment_method;
                $update['payment_channel'] = $invoiceData->getPaymentChannel() ?? $order->payment_channel;
                $update['payment_destination'] = $invoiceData->getPaymentDestination() ?? $order->payment_destination;

                Log::info("InvoiceData:", (array) $invoiceData);

                $order->update($update);

                Log::info("Payment data updated dari Xendit:", $update);
            }
            else {
                Log::warning("Invoice Not Found di Xendit", [
                    'external_id' => $invoice
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Gagal mengambil status invoice", ['message' => $e->getMessage()]);
        }

        Log::info("BERHASIL menuju halaman success");

        return view('web.payment.status', compact('order'));
    }

    public function cancel(Request $request, $invoice)
    {
        $order = OrderMerch::where('invoice', $invoice)->firstOrFail();

        // Proteksi: hanya bisa cancel kalau masih pending
        if ($order->status !== 'pending') {
            return redirect()
                ->route('payment.status', $invoice)
                ->with('error', 'Pesanan tidak bisa dibatalkan.');
        }

        $order->update([
            'status' => 'cancelled'
        ]);

        Log::info('Pesanan dibatalkan oleh user', [
            'invoice' => $invoice
        ]);

        return redirect()
            ->route('payment.status', $invoice)
            ->with('success', 'Pesanan berhasil dibatalkan.');
    }
}
