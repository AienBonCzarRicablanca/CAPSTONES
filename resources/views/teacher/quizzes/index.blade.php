<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4 min-w-0">
            @if($class->photo_path)
                <img class="h-12 w-12 rounded-lg object-cover ring-2 ring-white shadow-sm" src="{{ Storage::url($class->photo_path) }}" alt="Class picture" />
            @else
                <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center ring-2 ring-white shadow-sm">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            @endif
            <div>
                <div class="text-sm text-gray-600">Quizzes</div>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">{{ $class->name }}</h2>
            </div>
        </div>
    </x-slot>

    <x-class-nav :class="$class" active="quizzes" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500">
                                <th class="py-2">Title</th>
                                <th class="py-2">Due</th>
                                <th class="py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($quizzes as $q)
                                <tr class="border-t">
                                    <td class="py-3 font-medium">{{ $q->title }}</td>
                                    <td class="py-3 text-gray-600">{{ $q->due_at?->format('M d, Y') ?? '—' }}</td>
                                    <td class="py-3">
                                        <div class="flex items-center gap-3">
                                            <a class="text-indigo-600 hover:underline" href="{{ route('teacher.quizzes.show', [$class, $q]) }}">Open</a>
                                            <form method="POST" action="{{ route('teacher.quizzes.destroy', [$class, $q]) }}" onsubmit="return confirm('Delete this quiz? All questions and attempts will be deleted.')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="py-6 text-gray-500">No quizzes.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $quizzes->links() }}</div>
                </div>
                
                <div class="p-6 border-t border-gray-200">
                    <a href="{{ route('teacher.quizzes.create', $class) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium shadow-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create Quiz
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
