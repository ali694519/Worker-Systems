<?php
namespace App\Repository;

use App\Models\ClientOrder;
use App\Interfaces\CrudRepoInterface;

class  ClientOrderRepo implements CrudRepoInterface {

    public function store($request) {
        $clientId = auth("client")->id();
        if(ClientOrder::where("client_id",$clientId)->where("post_id",$request->post_id)->exists()) {
            return response()->json([
            "message"=>"duplicate order request"
        ],406);
        }
        $data = $request->all();
        $data['client_id'] = $clientId;
        $order = ClientOrder::create($data);
          return response()->json([
            "message"=>"success"
        ],200);
    }

    //  public function show()
    // {
    //     $orders = ClientOrder::with('post', 'client')->whereStatus('pending')->whereHas('post', function ($query) {
    //         $query->where('worker_id', auth()->guard('worker')->id());
    //     })->get();
    //     return response()->json([
    //         "orders" => $orders
    //     ]);
    // }

    public function update($id, $request)
    {
        $order = ClientOrder::findOrFail($id);
        $order->setAttribute('status', $request->status)->save();
        // $order->update(['status' => $request->status]);
        return response()->json([
            "message" => "updated"
        ]);
    }
    // public function approvedOrders()
    // {
    //     $orders = ClientOrder::with('post')->whereStatus('approved')->where('client_id', auth()->guard('client')->id())->get();
    //     return response()->json([
    //         "orders" => $orders
    //     ]);
    // }
}
