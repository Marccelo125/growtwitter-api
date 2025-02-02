<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retweet extends Model
{
    use HasFactory;
    protected $fillable = [
        'userId',
        'postId',
        'content'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }
    public function post()
    {
        return $this->belongsTo(Post::class, 'postId');
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'postId');
    }
}
