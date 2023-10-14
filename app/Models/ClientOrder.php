<?php

namespace App\Models;

use App\Models\Post;
use App\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClientOrder extends Model
{
    use HasFactory;
    protected $fillable = ["client_id","post_id"];
    protected $guarded = ["status"];

    public function client() {
        return $this->belongsTo(Client::class)
        ->select('id','name');
    }

      public function post() {
        return $this->belongsTo(Post::class)
        ->select('id','content');
    }
}
