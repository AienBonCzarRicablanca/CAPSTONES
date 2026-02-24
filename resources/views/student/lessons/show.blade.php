<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $lesson->title }}</h2>
                <div class="text-sm text-gray-500">Class: {{ $class->name }}</div>
            </div>
            <a class="text-indigo-600 hover:underline" href="{{ route('student.lessons.index', $class) }}">Back</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">
                    <div class="prose max-w-none">{!! nl2br(e($lesson->content ?? '')) !!}</div>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">
                    <h3 class="font-semibold">Materials</h3>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500">
                                    <th class="py-2">Title</th>
                                    <th class="py-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lesson->materials as $m)
                                    <tr class="border-t">
                                        <td class="py-3">{{ $m->title }}</td>
                                        <td class="py-3">
                                            <a class="text-indigo-600 hover:underline" href="{{ route('materials.stream', $m) }}" target="_blank" rel="noopener">Open</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="py-6 text-gray-500">No materials.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
