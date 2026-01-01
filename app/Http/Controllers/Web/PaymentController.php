<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\CreateInvoiceRequest;
use Xendit\Invoice\InvoiceCallback;
use Xendit\XenditSdkException;
use App\OrderMerch;
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

        $order = OrderMerch::where('invoice', $invoice)->firstOrFail();
        Log::info('Order ditemukan', ['order_id' => $order->id]);

        if ($order->status !== 'pending') {
            return redirect()
                ->route('payment.status', $invoice)
                ->with('error', 'Pesanan tidak dapat dibayar.');
        }

        try {
            // Set API key untuk Xendit PHP SDK v3.x
            $config = \Xendit\Configuration::getDefaultConfiguration()
                ->setApiKey(env('XENDIT_SECRET_KEY'));

            Log::info('Xendit API key diset');

            // InvoiceApi HARUS pakai Guzzle + Config
            $apiInstance = new InvoiceApi(
                new \GuzzleHttp\Client(),
                $config
            );

            Log::info('InvoiceApi instance dibuat');

            $createInvoiceRequest = new CreateInvoiceRequest([
                'external_id' => $order->invoice,
                'amount' => $order->total_tagihan,
                'payer_email' => auth()->user()->email,
                'description' => 'Pembayaran ' . $order->invoice,
                'currency' => 'IDR',
                'invoice_duration' => 60, //1 jam

                // Redirect setelah bayar
                'success_redirect_url' => url('/payment/status/' . $order->invoice),
                'failure_redirect_url' => url('/payment/status/' . $order->invoice),
                
                'customer_notification_preference' => [
                    'invoice_created' => ['email'],
                    'invoice_reminder' => ['email'],
                    'invoice_paid' => ['email'],
                ],
            ]);

            $xenditInvoice = $apiInstance->createInvoice($createInvoiceRequest);

            Log::info('Invoice berhasil dibuat', [
                'invoice_url' => $xenditInvoice->getInvoiceUrl()
            ]);

            $order->update([
                'payment_url' => $xenditInvoice->getInvoiceUrl(),
                'status' => 'pending',
            ]);

            return redirect($xenditInvoice->getInvoiceUrl());

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

        if (!$order) {
            Log::error("Order tidak ditemukan: " . $externalId);
            return response()->json(['message' => 'Order not found'], 404);
        }

        switch ($status) {
            case 'PAID':
                $order->update([
                    'status'  => 'success',
                    'paid_at' => now(),
                ]);
                break;
            case 'EXPIRED':
                $order->update(['status' => 'expired']);
                break;
            case 'FAILED':
                $order->update(['status' => 'cancelled']);
                break;
            default:
                $order->update([
                    'status' => 'pending'
                ]);
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
        if ($status === 'PAID') {

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
        $order = OrderMerch::where('invoice', $invoice)->firstOrFail();

        Log::info("Halaman success dipanggil", [
            'invoice' => $invoice,
            'current_status' => $order->status
        ]);

        // Jika sudah PAID dari webhook → tidak perlu cek Xendit lagi
        if ($order->status === 'success') {
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
