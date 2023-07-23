<x-layout>

    <div class="container py-md-5 container--narrow">
        <h2>
          <img class="avatar-small" src="{{$avatar}}" /> {{$username}}
          <form class="ml-2 d-inline" action="#" method="POST">
            @if (auth()->user()->username == $username)
                <a href="/manage-avatar" class="btn btn-primary btn-sm">Manage Avatar</a>
            @endif
          </form>
          </h2>
        </h2>

        <div class="profile-nav nav nav-tabs pt-2 mb-4">
          <a href="#" class="profile-nav-link nav-item nav-link active">Posts: {{$postCount}}</a>
        </div>

        <div class="list-group">
            @foreach ($posts as $post)
            <a href="/post/{{$post->id}}" class="list-group-item list-group-item-action">
                <img class="avatar-tiny" src="{{$post->user->avatar}}" />
                <strong>{{$post->title}}</strong> on {{$post->created_at->format('d/m/Y')}}
            </a>
            @endforeach
        </div>
      </div>

</x-layout>
