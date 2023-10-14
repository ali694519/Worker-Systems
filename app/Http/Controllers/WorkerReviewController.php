<?php

namespace App\Http\Controllers;

use App\Models\WorkerReview;
use App\Http\Requests\Worker\WorkerReviewRequest;
use App\Http\Resources\Worker\WorkerReviewResource;

class WorkerReviewController extends Controller
{
    public function store(WorkerReviewRequest $request)
    {
        $data = $request->all();
        $data['client_id'] = auth()->guard('client')->id();
        $reviews = WorkerReview::create($data);
        return response()->json([
            "data" => $reviews
        ]);
    }

    public function postRate($id)
    {
        $reviews = WorkerReview::wherePostId($id);
        $average = $reviews->sum('rate') / $reviews->count();

        return response()->json([
            "total_rate" => round($average, 1),
            "data" => WorkerReviewResource::collection($reviews->get())
        ]);
    }
}
