<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Bid;
use App\User;
use App\Products;
use App\Events\MessageSent;
use App\Events\BidSent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Jobs\SendNotifacationBid;

class ChatsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Show chats
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Fetch all messages
     *
     * @return Message
     */
    public function fetchMessages($slug)
    {
        $product = Products::where('slug',$slug)->first();
        $bid = Bid::with('user')->where('product_id',$product->id)->orderBy('created_at', 'desc')->get();
        $bids = $bid->map(function($data){
            return [
                'user'=>$data->user,
                'message'=>$data->price,
                'produk'=>$data->product_id,
                'tanggal'=> Carbon::parse($data->created_at)->format('Y-m-d H:i:s')
            ];
        });
        if (in_array(config('app.env'), ['local', 'testing', 'development'])) {
            \Log::info('[fetchMessages] Response:', $bids->toArray());
        }
        return $bids;
    }

    /**
     * Get realtime state for a product: highest bid, next nominals, latest messages
     */
    public function state($slug)
    {
        $product = Products::where('slug',$slug)->firstOrFail();
        // Ambil nilai tertinggi (MAX) dari bids — lebih aman untuk sinkronisasi
        // Pastikan max dihitung secara numerik (hindari max string yang lexicographic)
        $highestFromBids = (int) Bid::where('product_id', $product->id)->max(\DB::raw('price+0'));
        $highest = $highestFromBids > 0 ? $highestFromBids : (int) $product->price;
        $step = (int) ($product->kelipatan ?? 10000);
        if ($step <= 0) { $step = 10000; }

        // Next nominals: 5x kelipatan dari harga tertinggi
        $nextNominals = [];
        for ($i = 1; $i <= 5; $i++) {
            $nextNominals[] = $highest + ($step * $i);
        }

        // Data bid terbaru untuk riwayat (ambil 1 terbaru jika ada)
        $messages = [];
        $latestBid = Bid::with('user')
            ->where('product_id', $product->id)
            ->orderBy('created_at', 'desc')
            ->first();
        if ($latestBid) {
            $messages[] = [
                'user' => [
                    'id' => $latestBid->user->id ?? 0,
                    'name' => $latestBid->user->name ?? 'User',
                    'email' => $latestBid->user->email ?? '-' ,
                ],
                'message' => (int) $latestBid->price,
                'tanggal' => \Carbon\Carbon::parse($latestBid->created_at)->format('Y-m-d H:i:s')
            ];
        }

        $response = [
            'highest' => $highest,
            'step' => $step,
            'nextNominals' => $nextNominals,
            'messages' => $messages,
            'productId' => (int) $product->id,
            'slug' => $slug,
        ];
        if (in_array(config('app.env'), ['local', 'testing', 'development'])) {
            \Log::info('[state] Optimized Response:', $response);
        }
        return response()->json($response);
    }

    /**
     * Persist message to database
     *
     * @param  Request $request
     * @return Response
     */
    public function sendMessage(Request $request)
    {
        try {
                // Input
                $priceBid = (int) $request->input('message');
                $productID = (int) $request->input('produk');
                $user = Auth::user();

                // Validasi awal input
                if (!$priceBid || !$productID || !$user) {
                    return response()->json(['status' => 'error', 'message' => 'Input tidak valid'], 422);
                }

                // Transaksi untuk mencegah tabrakan bid (race condition)
                $result = DB::transaction(function() use ($request, $user, $productID, &$newHighest, $priceBid) {
                    // Kunci baris product untuk update agar serial per product
                    $product = Products::where('id', $productID)->lockForUpdate()->first();
                    if (!$product) {
                        return response()->json(['status' => 'error', 'message' => 'Produk tidak ditemukan'], 404);
                    }

                    // Ambil highest secara numerik untuk validasi kelipatan
                    $currentHighest = (int) Bid::where('product_id', $productID)->max(\DB::raw('price+0'));
                    if ($currentHighest <= 0) {
                        $currentHighest = (int) $product->price;
                    }

                    // Kelipatan
                    $step = (int) ($product->kelipatan_bid ?? $product->kelipatan ?? 10000);
                    if ($step <= 0) { $step = 10000; }

                    // Tolak jika sama dengan harga yang sudah ada (duplicate), atau lebih kecil/sama dari highest
                    if ($priceBid <= $currentHighest) {
                        return response()->json(['status' => 'error', 'message' => 'Bid harus lebih tinggi dari harga tertinggi saat ini'], 422);
                    }

                    // Wajib sesuai kelipatan dari highest
                    $diff = $priceBid - $currentHighest;
                    if ($diff % $step !== 0) {
                        return response()->json(['status' => 'error', 'message' => 'Bid harus sesuai kelipatan yang ditentukan'], 422);
                    }

                    // Cek lagi jika harga sama untuk produk ini
                    $checkBid = Bid::where('product_id', $productID)->where('price', $priceBid)->first();
                    if (!empty($checkBid)) {
                        return response()->json(['status' => 'error', 'message' => 'Harga bid ini sudah diambil user lain'], 409);
                    }

                    // Simpan bid ke DB dan gunakan model hasil simpan
                    $bid = Bid::create([
                        'product_id' => $productID,
                        'user_id'    => $user->id,
                        'price'      => $priceBid,
                    ]);

                    Log::info('[BID] New bid created', [
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'price' => $bid->price,
                        'product_id' => $productID,
                    ]);

                    // Broadcast — lakukan sebagai aksi, jangan assign hasilnya
                    broadcast(new MessageSent($user, $bid->price, $bid->created_at, $productID))->toOthers();
                    broadcast(new BidSent($bid->price, $productID))->toOthers();

                    // Tetapkan highest menggunakan hasil simpan
                    $newHighest = (int) $bid->price;

                    return [
                        'status' => 'Message Sent!',
                        'data' => [
                            'user' => [
                                'id' => $user->id,
                                'name' => $user->name,
                                'email' => $user->email,
                            ],
                            'message' => $bid->price,
                            'produk' => $bid->product_id,
                            'tanggal' => Carbon::parse($bid->created_at)->format('Y-m-d H:i:s'),
                            'highest' => $newHighest,
                        ],
                    ];
                });

                if (is_array($result)) {
                    if (in_array(config('app.env'), ['local', 'testing', 'development'])) {
                        Log::info('[sendMessage] Response:', $result);
                    }
                    return $result;
                }
                // Jika result adalah Response (error), kembalikan langsung
                if (in_array(config('app.env'), ['local', 'testing', 'development'])) {
                    Log::info('[sendMessage] Error Response:', (array) $result);
                }
                return $result;
            
        } catch (Exception $e) {
             \Log::error("Save error ".$e->getMessage());
             return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat menyimpan bid'], 500);
        }

    }
    // public function sendNotificationBid(Bid $bid)
    // {
    //      SendNotifacationBid::dispatch($bid)->onQueue('notifikasi')->delay(now());
    // }
}