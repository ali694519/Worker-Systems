<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Worker;
use App\Models\WorkerReview;
use Illuminate\Http\Request;
use App\Http\Requests\Worker\UpdatingProfileRequest;
use App\Services\WorkerService\UpdatingProfileService;

class WorkerProfileController extends Controller
{
     public function userProfile() {
        $workerId = auth('worker')->id();
        $worker = Worker::with(['posts'])
        ->find($workerId)
        ->makeHidden('status','verified_at','verification_token');
        $reviews = WorkerReview::WhereIn("post_id",$worker->posts()->pluck("id"))->get();
        $rate = round($reviews->sum('rate')/ $reviews->count(),1);
        return response()->json([
            "data"=>array_merge($worker->toArray(),['rate'=>$rate]),
        ],200);
    }

    public function edit() {
        return response()->json([
            "worker"=>Worker::find(auth('worker')->id())
        ]);
    }

    public function update(UpdatingProfileRequest $request) {
        return (new UpdatingProfileService())->update($request);
    }

      public function delete() {
        Post::where('worker_id',auth('worker')->id())->delete();
          return response()->json([
            "message" => "deleted"
        ]);
    }
}
