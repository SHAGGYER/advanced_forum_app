<body>
    <h1>Hi, {{ $user->name }}!</h1>

    <p>
        This is just to notify that somebody posted in the thread: <strong>{{ $thread->title }}</strong>
        at {{ $thread->updated_at }}!
    </p>
</body>