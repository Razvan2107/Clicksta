<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\Laravel\Facades\Image;

class ProfilesController extends Controller
{
    public function index(User $user){
        $follows = (auth()->user()) ? auth()->user()->following->contains($user->id) : false;

        $postCount = Cache::remember(
            'count.posts.' . $user->id, 
            now()->addSeconds(30), 
            function() use ($user) {
                return $user->posts->count();
            });

        $followersCount = Cache::remember(
            'count.followers.' . $user->id, 
            now()->addSeconds(30), 
            function() use ($user) {
                return $user->profile->followers->count();
            });
            
        $followingCount = Cache::remember(
            'count.following.' . $user->id, 
            now()->addSeconds(30), 
            function() use ($user) {
                return $user->following->count();
            });

        return view('profiles.index', compact('user', 'follows', 'postCount', 'followersCount', 'followingCount'));
    }

    public function edit(User $user){
        $this->authorize('update',$user->profile);
        return view('profiles.edit', compact('user'));
    }

    public function update(User $user){
        $this->authorize('update',$user->profile);

        $data = request()->validate([
            'title' => 'required',
            'description' => 'required',
            'url' => 'url',
            'image' => '',
        ]);

        $imagePath = null;

        if(request('image')){
            $imagePath = request('image')->store('profile','public');

            $image = Image::read(public_path("storage/{$imagePath}"))->resize(1200,1200);
            $image->save();
        }

        if($imagePath != null)
            auth()->user()->profile->update(array_merge(
                $data,
                ['image' => $imagePath]
            ));
        else
            auth()->user()->profile->update($data);
            

        return redirect("/profile/{$user->id}");
    }
}
