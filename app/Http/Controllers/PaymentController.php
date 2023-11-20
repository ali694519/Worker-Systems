<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Order;
use App\Models\ClientOrder;
use Illuminate\Http\Request;

class PaymentController extends Controller
{

    public function checkout($id)
    {
        $post_id = Post::findOrFail($id)->whereStatus('approved')->first();
        // Set Stripe API key
        \Stripe\Stripe::setApiKey(config('services.stripe.secret_key'));
        $checkout_session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [
            [
            'price_data' => [
                'currency' => 'usd',
                'product_data' => [
                    'description' => $post_id->content,
                    'name' => $post_id->id,
                ],
                'unit_amount' => $post_id->price*100, // Price in cents (e.g., 100 cents = $1.00)
            ],
            'quantity' => 1,
            ],
        ],
        'mode' => 'payment',
        'success_url' => route('checkout.success', [], true)."?session_id={CHECKOUT_SESSION_ID}",
        'cancel_url' => route('checkout.cancel', [], true),
    ]);
        // // Create order record
        $order = new Order();
        $order->status = "unpaid";
        $order->total_price = $post_id->price;
        $order->session_id = $checkout_session->id;
        $order->save();
        // Redirect to Stripe Checkout URL
        return response()->json([
        'checkout_url' => $checkout_session->url
        ]);
    }
    public function success(Request $request)
    {
         try {
            //  $session =  \Stripe\Checkout\Session::retrieve($session_id);
            $session = $request->query('session_id');
            if(!$session) {
                abort(404);
            }
            $order = Order::where('session_id',$session)->where('status','unpaid')->first();
            if(!$order) {
                abort(404);
            }
            $order->status = "paid";
            $order->save();
        } catch (ModelNotFoundException  $th) {
            throw new NotFoundHttpException();
        }
        return response()->json(['message'=>'payment success'], 200);

    }

    public function cancel()
    {
        return response()->json(['message'=>'payment cancel'], 404);
    }
}
