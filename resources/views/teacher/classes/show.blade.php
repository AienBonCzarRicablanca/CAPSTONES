<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="flex items-center gap-4 min-w-0">
                @if($class->photo_path)
                    <img class="h-16 w-16 rounded-lg object-cover ring-2 ring-white shadow-sm" src="{{ Storage::url($class->photo_path) }}" alt="Class picture" />
                @else
                    <div class="h-16 w-16 rounded-lg bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center ring-2 ring-white shadow-sm">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                @endif

                <div class="min-w-0">
                    <h2 class="font-semibold text-2xl text-gray-900 leading-tight break-words">{{ $class->name }}</h2>
                    <div class="mt-1 flex items-center gap-3 text-sm text-gray-600">
                        <span class="inline-flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            {{ $class->students_count ?? $class->students->count() }} students
                        </span>
                        <span class="text-gray-400">•</span>
                        <span class="inline-flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($class->is_private)
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                @endif
                            </svg>
                            {{ $class->is_private ? 'Private' : 'Public' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="sm:text-right">
                <div class="flex items-center gap-3 sm:justify-end mb-3">
                    <a class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition-colors" 
                       href="{{ route('teacher.classes.edit', $class) }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Manage Class
                    </a>
                </div>
                <div class="bg-gray-50 rounded-lg px-4 py-3 border border-gray-200">
                    <div class="text-xs font-medium text-gray-600 uppercase tracking-wider mb-1">Join Code</div>
                    <div class="font-mono text-2xl font-bold text-gray-900 tracking-wider">{{ $class->join_code }}</div>
                    <form method="POST" action="{{ route('teacher.classes.regenerateCode', $class) }}" class="mt-2">
                        @csrf
                        <button class="text-xs text-indigo-600 hover:text-indigo-700 font-medium" type="submit">
                            Regenerate code
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </x-slot>

    <x-class-nav :class="$class" active="students" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">

                <div class="p-6 space-y-6">
                    <div>
                        <h3 class="font-semibold">Students</h3>
                        <div class="mt-3 overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left text-gray-500">
                                        <th class="py-2">Name</th>
                                        <th class="py-2">Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($class->students as $student)
                                        <tr class="border-t">
                                            <td class="py-3">{{ $student->name }}</td>
                                            <td class="py-3 text-gray-600">{{ $student->email }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="py-6 text-gray-500">No students enrolled yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="rounded-lg border p-4">
                        <div class="font-semibold text-gray-900">Add student</div>
                        <form method="POST" action="{{ route('teacher.classes.students.add', $class) }}" class="mt-3 space-y-2">
                            @csrf
                            <div>
                                <x-input-label for="student_email" value="Student email" />
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                    <x-text-input id="student_email" name="student_email" type="email" class="mt-1 block w-full" value="{{ old('student_email') }}" required />
                                    <x-primary-button class="sm:mt-1 sm:self-auto self-end">Add</x-primary-button>
                                </div>
                                <x-input-error :messages="$errors->get('student_email')" class="mt-2" />
                            </div>
                        </form>
                        <div class="mt-2 text-xs text-gray-500">Adds the student immediately (approved).</div>
                    </div>

                    @if($class->is_private)
                        <div>
                            <h3 class="font-semibold">Join Requests</h3>
                            <div class="mt-3 overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead>
                                        <tr class="text-left text-gray-500">
                                            <th class="py-2">Name</th>
                                            <th class="py-2">Email</th>
                                            <th class="py-2">Requested</th>
                                            <th class="py-2">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($class->pendingStudents as $student)
                                            <tr class="border-t">
                                                <td class="py-3">{{ $student->name }}</td>
                                                <td class="py-3 text-gray-600">{{ $student->email }}</td>
                                                <td class="py-3 text-gray-600">{{ optional($student->pivot?->requested_at)->format('M d, Y h:i A') ?? '—' }}</td>
                                                <td class="py-3">
                                                    <div class="flex items-center gap-3">
                                                        <form method="POST" action="{{ route('teacher.classes.requests.approve', [$class, $student]) }}">
                                                            @csrf
                                                            @method('PUT')
                                                            <button class="text-green-700 hover:underline" type="submit">Accept</button>
                                                        </form>
                                                        <form method="POST" action="{{ route('teacher.classes.requests.reject', [$class, $student]) }}" onsubmit="return confirm('Reject this join request?')">
                                                            @csrf
                                                            @method('PUT')
                                                            <button class="text-red-700 hover:underline" type="submit">Reject</button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="py-6 text-gray-500">No pending requests.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
