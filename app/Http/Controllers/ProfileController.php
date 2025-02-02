<?php

namespace App\Http\Controllers;

use App\Models\Follower;
use App\Models\Post;
use App\Models\Retweet;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(string $id)
    {
        try {
            $user = User::with('posts')->withCount(["posts", "retweets"])->findOrFail($id);

            $osQueEuSigo = Follower::where('followerId', $id)->count();
            $quemSegueEle = Follower::where('followingId', $id)->count();

            $osQueEuSigoData = Follower::with('follower', 'following', 'posts')->where('followerId', $id)->paginate(20);
            $quemSegueEleData = Follower::with('following', 'follower', 'posts')->where('followingId', $id)->paginate(20);

            $posts = Post::with([
                'user:id,username,name,avatar_url',
                'likes',
                'retweets',
                'comments' => function ($query) {
                    $query->with('user'); // carregar o usuário que fez o comentário
                }
            ])
                ->withCount('likes', 'comments')
                ->latest()->paginate(20);


            $retweets = Retweet::with([
                'user',
                'post' => function ($query) {
                    $query->with([
                        'user:id,username,name,avatar_url',
                        'likes',
                        'retweets',
                        'comments' => function ($query) {
                            $query->with('user'); // carregar o usuário que fez o comentário
                        }
                    ])->withCount('likes', 'comments');
                }
            ])->latest()->paginate(20);


            return response()->json([
                'success' => true,
                'msg' => 'Usuário encontrado com sucesso',
                'data' => [
                    'user' => $user,
                    'followings' => $osQueEuSigo,
                    'followers' => $quemSegueEle,
                    'followingsData' => $osQueEuSigoData,
                    'followersData' => $quemSegueEleData,
                    'posts' => $posts,
                    'retweets' => $retweets
                ]
            ], 200);

        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'msg' => $th->getMessage()], 500);
        }
    }

}
