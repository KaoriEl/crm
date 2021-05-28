<h2>Вы были назначены ответственным по задаче {{ $post->title }}</h2>

<p>Вы можете открыть задачу, перейдя по ссылке: <a href="/posts/{{ $post->id }}"><?=env('APP_URL', 'http://edit.marketica-dev.ru')?>/posts/{{ $post->id }}</a></p>