<?php

namespace App\Http\Controllers;

use App\Interfaces\CrudRepoInterface;
use App\Http\Requests\Client\ClientOrderRequest;
use App\Models\ClientOrder;
use Illuminate\Http\Request;


class ClientOrderController extends Controller
{
    protected $crudRepo;
    public function __construct(CrudRepoInterface $crudRepo)
    {
        $this->crudRepo = $crudRepo;
    }

    public function addOrder(ClientOrderRequest $request){
        return $this->crudRepo->store($request);
    }

    public function workerOrder(){
        $orders = ClientOrder::with('client','post')
        ->whereStatus('pending')
        ->whereHas('post',function($query) {
            $query->where("worker_id",auth("worker")->id());
        })->get();
        return response()->json([
            "orders"=>$orders
        ]);
    }
    // public function workerOrder()
    // {
    //     return $this->crudRepo->show();
    // }

    public function update($id, Request $request)
    {
        return $this->crudRepo->update($id, $request);
    }

}
