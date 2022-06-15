<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex flex-row gap-2 items-center">
            {{ $group->name }}
            <form action="{{ route('dashboard.group.destroy', ['group' => $group]) }}" method="POST">
                @csrf
                @method('delete')
                <button type="submit" class="text-red-600 font-extrabold">X</button>
            </form>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm p-4">

                <h5 class="mb-4">People</h5>

                <div class="bg-white border-b border-gray-200">

                    @foreach($group->people as $person)

                    <p class="text-gray-500 mb-2">{{ $person->name }}</p>

                    @endforeach

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
