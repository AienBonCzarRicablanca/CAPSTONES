<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">To Do / Activities</h2>
            <div class="text-sm text-gray-500">Quizzes and activities across all your classes</div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8" x-data="{ tab: 'ongoing' }">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="border-b border-gray-200">
                    <nav class="flex gap-6 px-6 overflow-x-auto whitespace-nowrap" aria-label="Tabs">
                        <button type="button" @click="tab = 'ongoing'" class="py-4 text-sm font-medium" :class="tab === 'ongoing' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 hover:text-gray-700'">
                            Ongoing
                        </button>
                        <button type="button" @click="tab = 'due'" class="py-4 text-sm font-medium" :class="tab === 'due' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 hover:text-gray-700'">
                            Due
                        </button>
                        <button type="button" @click="tab = 'completed'" class="py-4 text-sm font-medium" :class="tab === 'completed' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 hover:text-gray-700'">
                            Completed
                        </button>
                    </nav>
                </div>

                <div class="p-6" x-show="tab === 'ongoing'" x-cloak>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500">
                                    <th class="py-2">Type</th>
                                    <th class="py-2">Title</th>
                                    <th class="py-2">Class</th>
                                    <th class="py-2">Due</th>
                                    <th class="py-2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ongoing as $item)
                                    <tr class="border-t">
                                        <td class="py-3">
                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-800">
                                                {{ $item['kind'] === 'QUIZ' ? 'Quiz' : 'Activity' }}
                                            </span>
                                        </td>
                                        <td class="py-3 font-medium text-gray-900">{{ $item['title'] }}</td>
                                        <td class="py-3 text-gray-600">{{ $item['class_name'] ?? '—' }}</td>
                                        <td class="py-3 text-gray-600">{{ $item['due_at']?->format('M d, Y h:i A') ?? '—' }}</td>
                                        <td class="py-3">
                                            <a class="text-indigo-600 hover:underline" href="{{ $item['url'] }}">Open</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="py-6 text-gray-500">No ongoing items.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="p-6" x-show="tab === 'due'" x-cloak>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500">
                                    <th class="py-2">Type</th>
                                    <th class="py-2">Title</th>
                                    <th class="py-2">Class</th>
                                    <th class="py-2">Due</th>
                                    <th class="py-2">Notes</th>
                                    <th class="py-2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($due as $item)
                                    <tr class="border-t">
                                        <td class="py-3">
                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-800">
                                                {{ $item['kind'] === 'QUIZ' ? 'Quiz' : 'Activity' }}
                                            </span>
                                        </td>
                                        <td class="py-3 font-medium text-gray-900">{{ $item['title'] }}</td>
                                        <td class="py-3 text-gray-600">{{ $item['class_name'] ?? '—' }}</td>
                                        <td class="py-3 text-gray-600">{{ $item['due_at']?->format('M d, Y h:i A') ?? '—' }}</td>
                                        <td class="py-3 text-gray-600">
                                            @if(($item['kind'] ?? null) === 'ACTIVITY' && ($item['allow_late'] ?? false))
                                                <span class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800">Late allowed</span>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            <a class="text-indigo-600 hover:underline" href="{{ $item['url'] }}">Open</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="py-6 text-gray-500">No due items.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="p-6" x-show="tab === 'completed'" x-cloak>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500">
                                    <th class="py-2">Type</th>
                                    <th class="py-2">Title</th>
                                    <th class="py-2">Class</th>
                                    <th class="py-2">Submitted</th>
                                    <th class="py-2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($completed as $item)
                                    <tr class="border-t">
                                        <td class="py-3">
                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-800">
                                                {{ $item['kind'] === 'QUIZ' ? 'Quiz' : 'Activity' }}
                                            </span>
                                            @if(($item['kind'] ?? null) === 'ACTIVITY' && ($item['is_late'] ?? false))
                                                <span class="ms-2 inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800">Late</span>
                                            @endif
                                        </td>
                                        <td class="py-3 font-medium text-gray-900">{{ $item['title'] }}</td>
                                        <td class="py-3 text-gray-600">{{ $item['class_name'] ?? '—' }}</td>
                                        <td class="py-3 text-gray-600">{{ $item['submitted_at']?->format('M d, Y h:i A') ?? '—' }}</td>
                                        <td class="py-3">
                                            <a class="text-indigo-600 hover:underline" href="{{ $item['url'] }}">Open</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="py-6 text-gray-500">No completed items yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
