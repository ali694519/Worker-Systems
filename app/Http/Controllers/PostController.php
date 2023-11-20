<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Filters\PostFilter;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Requests\StorePostRequest;
use App\Services\PostService\StoringPostService;

class PostController extends Controller
{
    function store(StorePostRequest $request)
    {
        return (new StoringPostService())->store($request);
    }
    public function index()
    {
        $posts = Post::all();
        return response()->json([
            "posts" => $posts
        ]);
    }

    public function show(Post $post)
    {
        return response()->json([
            "posts" => $post
        ]);
    }


    public function approved(Request $request)
    {
        $posts = QueryBuilder::for(Post::class)
            ->allowedFilters((new PostFilter)->filter())
            ->with('worker:id,name')
            ->where('status', 'approved')
            ->get(['id', 'content', 'price', 'worker_id']);
        return response()->json([
            "posts" => $posts
        ]);


    }
}
