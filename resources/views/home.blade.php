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
                        <a href="{{ route('dashboard.group.index') }}" class="text-sm text-gray-700 dark:text-gray-500 underline">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-gray-700 dark:text-gray-500 underline">Log in</a>
                    @endauth
                </div>
            @endif

            <div class="max-w-4xl w-full mx-auto sm:px-6 lg:px-8 gap-2 flex flex-col mt-7">
                <div tabindex="0" class="focus:outline-none bg-white p-6 shadow rounded">
                    <div class="flex items-center pb-3">
                        <div class="flex items-start justify-between w-full">
                            <div class="pl-3 w-full flex flex-col gap-3">
                                <p>Query Time: <b>{{ round($query_time, 3) }} ms</b></p>
                                <form class="flex flex-col gap-3" action="{{ route('home') }}" method="GET">
                                    <div>
                                        <div class="grid grid-cols-3 gap-6">
                                            <div class="col-span-3 sm:col-span-2">
                                                <label for="filter_groups[]" class="block text-sm font-medium text-gray-500">List(s)</label>
                                                @foreach($groups as $group)
                                                    <input type="checkbox" name="filter_groups[]" value="{{$group->id}}"
                                                        {{ collect(app('request')->input("filter_groups"))->contains($group->id) ? 'checked' : ''  }}> {{ $group->name }}
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <div class="grid grid-cols-3 gap-6">
                                            <div class="col-span-3 sm:col-span-2">
                                                <label for="network" class="block text-sm font-medium text-gray-500">Network(s)</label>
                                                @foreach($networks as $network)
                                                    <input type="checkbox" name="filter_networks[]" value="{{$network->value}}"
                                                        {{ collect(app('request')->input("filter_networks"))->contains($network->value) ? 'checked' : ''  }}> {{ $network->name }}
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-3 gap-6">
                                        <div>
                                            <div class="col-span-3 sm:col-span-2">
                                                <label for="filter_from" class="block text-sm font-medium text-gray-500">Posted After</label>
                                                <div class="mt-1 relative rounded-md shadow-sm">
                                                    <input type="date" name="filter_from" id="filter_from" value="{{ app('request')->input("filter_from") }}"
                                                              class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md">
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <div class="col-span-3 sm:col-span-2">
                                                <label for="filter_to" class="block text-sm font-medium text-gray-500">Posted Before</label>
                                                <div class="mt-1 relative rounded-md shadow-sm">
                                                    <input type="date" name="filter_to" id="filter_to" value="{{ app('request')->input("filter_to") }}"
                                                           class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <div class="grid grid-cols-3 gap-6">
                                            <div class="col-span-3 sm:col-span-2">
                                                <label for="filter_content" class="block text-sm font-medium text-gray-500">Text</label>
                                                <div class="mt-1 relative rounded-md shadow-sm">
                                                    <textarea type="text" name="filter_content" id="filter_content" maxlength="500"
                                                              class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md">{{ app('request')->input("filter_content") }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="text-left">
                                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">{{ __('Filter') }}</button>
                                        <a type="button" href="{{ route("home") }}" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">{{ __('Clear') }}</a>
                                    </div>
                                </form>

                                <div class="mt-3">{{ $posts->links() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-4xl w-full mx-auto sm:px-6 lg:px-8 gap-2 flex flex-col">

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
