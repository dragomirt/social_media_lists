<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Dragomir's Social Media Lists</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="{{ asset('css/app.css') }}">

        <script src="{{ asset('js/app.js') }}" defer></script>
    </head>
    <body class="antialiased">
        <div class="relative flex flex-col gap-5 items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center py-4 sm:pt-0">
            @if (Route::has('login'))
                <div class="hidden fixed top-0 right-0 px-6 py-4 sm:block">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-sm text-gray-700 dark:text-gray-500 underline">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-gray-700 dark:text-gray-500 underline">Log in</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="ml-4 text-sm text-gray-700 dark:text-gray-500 underline">Register</a>
                        @endif
                    @endauth
                </div>
            @endif

            <div class="w-full bg-white mx-auto sm:px-6 lg:px-8 gap-2 flex flex-col shadow">
                <p class="p-6">Total posts: {{ $posts->count() }}</p>
            </div>

            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 gap-2 flex flex-col">

                @foreach($posts as $post)
                    <div tabindex="0" class="focus:outline-none bg-white p-6 shadow rounded">
                        <div class="flex items-center border-b border-gray-200 pb-3">
                            <div class="flex items-start justify-between w-full">
                                <div class="pl-3 w-full">
                                    <p tabindex="0" class="focus:outline-none text-xl font-medium leading-5 text-gray-800">{{ $post->account->handle }} ({{ $post->account->network }}) <span class="text-sm">{{ $post->posted_at }}</span></p>
                                    <p tabindex="0" class="focus:outline-none text-sm leading-normal pt-2 text-gray-500">
                                        Original: <a href="{{ $post->url }}">{{ $post->url }}</a></p>

                                    <p tabindex="0" class="focus:outline-none text-sm leading-normal pt-2 text-gray-500">
                                        <div class="flex flex-row gap-2">
                                            <span class="text-gray-500 text-sm">Lists: </span>
                                            @foreach($post->account->person->groups as $group)
                                                <span class="text-gray-500 text-sm">{{ $group->name }} |</span>
                                            @endforeach
                                        </div>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="px-2">
                            <p tabindex="0" class="focus:outline-none text-sm leading-5 py-4 text-black-600">{{ $post->content }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </body>
</html>
