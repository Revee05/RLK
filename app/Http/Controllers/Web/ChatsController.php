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
        $bid = Bid::with('user')->where('product_id',$product->id)->get();
        $bids = $bid->map(function($data){
            return [
                'user'=>$data->user,
                'message'=>$data->price,
                'produk'=>$data->product_id,
                'tanggal'=> Carbon::parse($data->created_at)->format('Y-m-d h:m:s')
            ];
        });
        return $bids;
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

                    //histori bid
                    broadcast(new MessageSent($user, $bid, $bids->created_at))->toOthers();
                    //bid
                    event(new BidSent($bid));
                    

                    return ['status' => 'Message Sent!'];
                    // try {
                        
                    //     //send notification Bid
                    //     $this->sendNotificationBid($bids);
                        
                    // } catch (Exception $e) {
                    //     \Log::error("Failed send notifikasi ".$e->getMessage());
                    // }
                } else {
                    \Log::info("Bid Sudah ada");
                    return ['status' => 'Bid already!'];
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
