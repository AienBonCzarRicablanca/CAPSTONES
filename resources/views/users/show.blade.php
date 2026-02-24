<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">User Profile</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 flex items-center gap-4">
                    @if($user->profile_photo_path)
                        <img class="h-16 w-16 rounded-full object-cover" src="{{ Storage::url($user->profile_photo_path) }}" alt="Profile picture" />
                    @else
                        <div class="h-16 w-16 rounded-full bg-gray-200"></div>
                    @endif

                    <div class="min-w-0">
                        <div class="text-lg font-semibold text-gray-900 truncate">{{ $user->name }}</div>
                        <div class="text-sm text-gray-500">{{ strtoupper((string) optional($user->role)->name) }}</div>
                    </div>

                    <div class="ms-auto">
                        @if(auth()->id() !== $user->id)
                            <a href="{{ route('messages.show', $user) }}" class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white rounded-md text-xs uppercase tracking-widest">Message</a>
                        @else
                            <a href="{{ route('profile.edit') }}" class="text-indigo-600 hover:underline">Edit my profile</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
