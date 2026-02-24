<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">System Logs</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                        <div class="p-6">
                            <form method="GET" class="flex flex-col md:flex-row gap-3">
                                <div class="flex-1">
                                    <x-input-label value="Action (optional)" />
                                    <x-text-input name="action" class="mt-1 block w-full" value="{{ request('action') }}" />
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
                                        <th class="py-2">Time</th>
                                        <th class="py-2">User</th>
                                        <th class="py-2">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($logs as $log)
                                        <tr class="border-t align-top">
                                            <td class="py-3 text-gray-600 whitespace-nowrap">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                                            <td class="py-3">{{ $log->user?->name ?? '—' }}</td>
                                            <td class="py-3 font-medium">{{ $log->action }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="py-6 text-gray-500">No logs yet.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="mt-4">{{ $logs->links() }}</div>
                        </div>
                    </div>
        </div>
    </div>
</x-app-layout>
