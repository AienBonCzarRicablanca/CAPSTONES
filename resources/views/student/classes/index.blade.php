<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('My Classes') }}</h2>
            <a href="{{ route('student.classes.join') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Join Class</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse($classes as $class)
                            <div class="border rounded-lg p-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="font-semibold">{{ $class->name }}</div>
                                        <div class="text-sm text-gray-500">Teacher: {{ $class->teacher->name ?? '—' }}</div>
                                        <div class="text-xs text-gray-500">Privacy: {{ $class->is_private ? 'Private' : 'Public' }}</div>
                                    </div>
                                    @if($class->photo_path)
                                        <img class="h-12 w-12 rounded-full object-cover" src="{{ Storage::url($class->photo_path) }}" alt="Class picture" />
                                    @else
                                        <div class="h-12 w-12 rounded-full bg-gray-200"></div>
                                    @endif
                                </div>
                                <div class="mt-3">
                                    <a class="text-indigo-600 hover:underline" href="{{ route('student.lessons.index', $class) }}">View Lessons</a>
                                    <span class="text-gray-300 mx-2">|</span>
                                    <a class="text-indigo-600 hover:underline" href="{{ route('student.assignments.index', $class) }}">Assignments</a>
                                    <span class="text-gray-300 mx-2">|</span>
                                    <a class="text-indigo-600 hover:underline" href="{{ route('student.quizzes.index', $class) }}">Quizzes</a>
                                    <span class="text-gray-300 mx-2">|</span>
                                    <a class="text-indigo-600 hover:underline" href="{{ route('classes.chat.show', $class) }}">Open Class Chat</a>
                                </div>
                            </div>
                        @empty
                            <div class="text-gray-500">You are not enrolled in any class yet.</div>
                        @endforelse
                    </div>

                    <div class="mt-4">{{ $classes->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
