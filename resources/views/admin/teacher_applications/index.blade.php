<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Teacher Applications</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                        <div class="p-6">
                            <form method="GET" class="flex flex-col md:flex-row gap-3">
                                <div class="flex-1">
                                    <x-input-label value="Status" />
                                    <select name="status" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">All</option>
                                        <option value="PENDING" @selected(request('status') === 'PENDING')>PENDING</option>
                                        <option value="APPROVED" @selected(request('status') === 'APPROVED')>APPROVED</option>
                                        <option value="REJECTED" @selected(request('status') === 'REJECTED')>REJECTED</option>
                                    </select>
                                </div>
                                <div class="flex items-end">
                                    <x-primary-button>Filter</x-primary-button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                        <div class="p-6 overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left text-gray-500">
                                        <th class="py-2">User</th>
                                        <th class="py-2">Email</th>
                                        <th class="py-2">Status</th>
                                        <th class="py-2">Submitted</th>
                                        <th class="py-2">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($applications as $app)
                                        <tr class="border-t align-top">
                                            <td class="py-3 font-medium">{{ $app->user?->name ?? '—' }}</td>
                                            <td class="py-3 text-gray-600">{{ $app->user?->email ?? '—' }}</td>
                                            <td class="py-3">{{ $app->status }}</td>
                                            <td class="py-3 text-gray-600">{{ $app->created_at->format('Y-m-d') }}</td>
                                            <td class="py-3">
                                                @if($app->status === 'PENDING')
                                                    <div class="flex flex-col gap-2">
                                                        <form method="POST" action="{{ route('admin.teacherApplications.approve', $app) }}" class="flex items-center gap-2">
                                                            @csrf
                                                            @method('PUT')
                                                            <input name="review_notes" class="rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Notes (optional)" />
                                                            <button class="inline-flex items-center px-3 py-2 bg-gray-800 text-white rounded-md text-xs uppercase tracking-widest" type="submit">Approve</button>
                                                        </form>
                                                        <form method="POST" action="{{ route('admin.teacherApplications.reject', $app) }}" class="flex items-center gap-2">
                                                            @csrf
                                                            @method('PUT')
                                                            <input name="review_notes" class="rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Reason (optional)" />
                                                            <button class="inline-flex items-center px-3 py-2 bg-red-600 text-white rounded-md text-xs uppercase tracking-widest" type="submit">Reject</button>
                                                        </form>
                                                    </div>
                                                @else
                                                    <div class="text-gray-600">Reviewed {{ $app->reviewed_at?->format('Y-m-d') ?? '—' }}</div>
                                                    <div class="text-xs text-gray-500">{{ $app->review_notes ?? '' }}</div>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="py-6 text-gray-500">No applications.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>

                            <div class="mt-4">{{ $applications->links() }}</div>
                        </div>
                    </div>
        </div>
    </div>
</x-app-layout>
