@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
@endpush

@section('content')
<div class="container">
    <div class="row">
        <div class="col-3 p-5"><img src="{{$user->profile->profileImage()}}" class="rounded-circle w-100"></div>
        <div class="col-9 pt-5">
            <div class="d-flex justify-content-between align-items-baseline">
                <div class="d-flex align-items-center pb-3">
                    <div class="h4">{{$user->username}}</div>

                    <button id="followButton" class="btn btn-primary ml-4" @click="followUser" v-text="buttonText"></button>

                    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
                    <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>

                    <script>
                        new Vue({
                            el: '#followButton',

                            data: function() {
                                return {
                                    userId: '{{ $user->id }}',
                                    status: {{ $follows ? 'true' : 'false' }}
                                };
                            },

                            methods: {
                                followUser() {
                                    axios.post('/follow/' + this.userId)
                                        .then(response => {
                                            this.status = !this.status;
                                            console.log(response.data);
                                        })
                                        .catch(errors => {
                                            if (errors.response.status == 401) {
                                                window.location = '/login';
                                            }
                                        });
                                }
                            },
                            
                            computed: {
                                buttonText() {
                                    return this.status ? 'Unfollow' : 'Follow';
                                }
                            }
                        });
                    </script>

                </div>

                @can('update', $user->profile)
                    <a href="/p/create">Add New Post</a>
                @endcan
            </div>
            @can('update', $user->profile)
                <a href="/profile/{{$user->id}}/edit">Edit Profile</a>
            @endcan
            <div class="d-flex">
                <div class="pr-5"><strong>{{$postCount}}</strong> posts</div>
                <div class="pr-5"><strong>{{$followersCount}}</strong> followers</div>
                <div class="pr-5"><strong>{{$followingCount}}</strong> following</div>
            </div>
            <div class="pt-4"><b>{{$user->profile->title}}</b></div>
            <div>{{$user->profile->description}}</div>
            <div><a href="#">{{$user->profile->url}}</div>
        </div>
    </div>

    <div class="row pt-4">
        @foreach($user->posts as $post)
            <div class="col-4 pb-4">
                <a href="/p/{{$post->id}}">
                    <img src="/storage/{{$post->image}}" class="w-100 h-100">
                </a>
            </div>
        @endforeach
    </div>

</div>
@endsection
