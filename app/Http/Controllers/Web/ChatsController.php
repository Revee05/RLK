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
        return $bids;
    }

    /**
     * Get realtime state for a product: highest bid, next nominals, latest messages
     */
    public function state($slug)
    {
        $product = Products::where('slug',$slug)->firstOrFail();

        // Latest bids (newest first)
        $bids = Bid::with('user')
            ->where('product_id', $product->id)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        $messages = $bids->map(function ($data) {
            return [
                'user' => [
                    'id' => $data->user->id ?? 0,
                    'name' => $data->user->name ?? 'User',
                    'email' => $data->user->email ?? '-' ,
                ],
                'message' => (int) $data->price,
                'tanggal' => Carbon::parse($data->created_at)->format('Y-m-d H:i:s')
            ];
        });

        // Highest bid fallback to product price
        $highest = $bids->first() ? (int) $bids->first()->price : (int) $product->price;

        // Step (kelipatan)
        $step = (int) ($product->kelipatan_bid ?? $product->kelipatan ?? 10000);
        if ($step <= 0) { $step = 10000; }

        // Next nominals (5 options)
        $nextNominals = [];
        for ($i = 1; $i <= 5; $i++) {
            $nextNominals[] = $highest + ($step * $i);
        }

        return response()->json([
            'highest' => $highest,
            'step' => $step,
            'nextNominals' => $nextNominals,
            'messages' => $messages,
            'productId' => (int) $product->id,
            'slug' => $slug,
        ]);
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
                //Rikuest
                $priceBid = $request->input('message');
                $productID = $request->input('produk');
                
                $user = Auth::user();
                
                $checkBid = Bid::where('product_id',$productID)->where('price',$priceBid)->first();
                
                // jika bid belum ada...
                if (empty($checkBid)) {
                    $bids = $user->bid()->create([
                        'price' => $priceBid,
                        'product_id' => $productID,
                    ]);
                    $bid = $request->input('message');

                    \Log::info('[BID] New bid created', [
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'price' => $bid,
                        'product_id' => $productID
                    ]);

                    //histori bid - broadcast ke user lain
                    broadcast(new MessageSent($user, $bid, $bids->created_at, $productID))->toOthers();
                    \Log::info('[BID] MessageSent broadcasted to others');
                    
                    //bid - broadcast ke SEMUA user termasuk yang melakukan bid
                    broadcast(new BidSent($bid, $productID));
                    \Log::info('[BID] BidSent broadcasted to all users', ['price' => $bid]);

                    $response = [
                        'status' => 'Message Sent!',
                        'data' => [
                            'user' => [
                                'id' => $user->id,
                                'name' => $user->name,
                                'email' => $user->email,
                            ],
                            'message' => $bids->price,
                            'produk' => $bids->product_id,
                            'tanggal' => Carbon::parse($bids->created_at)->format('Y-m-d H:i:s')
                        ]
                    ];

                    if (in_array(config('app.env'), ['local', 'testing', 'development'])) {
                        \Log::info('Response:', $response);
                    }

                    return $response;
                } else {
                    \Log::info("Bid Sudah ada");
                    $response = [
                        'status' => 'Bid already!',
                        'data' => [
                            'user' => [
                                'id' => $checkBid->user->id,
                                'name' => $checkBid->user->name,
                                'email' => $checkBid->user->email,
                            ],
                            'message' => $checkBid->price,
                            'produk' => $checkBid->product_id,
                            'tanggal' => Carbon::parse($checkBid->created_at)->format('Y-m-d H:i:s')
                        ]
                    ];
                    if (in_array(config('app.env'), ['local', 'testing', 'development'])) {
                        \Log::info('Response:', $response);
                    }
                    return $response;
                }                
            
        } catch (Exception $e) {
             \Log::error("Save error ".$e->getMessage());
        }

    }
    // public function sendNotificationBid(Bid $bid)
    // {
    //      SendNotifacationBid::dispatch($bid)->onQueue('notifikasi')->delay(now());
    // }
}
