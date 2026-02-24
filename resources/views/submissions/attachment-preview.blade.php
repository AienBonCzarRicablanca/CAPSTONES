<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Submission Attachment</h2>
            <div class="text-sm text-gray-500">{{ $class->name }} • {{ $assignment->title }}</div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 space-y-4">
                    @php
                        $mimeString = (string) ($mime ?? '');
                        $hasPreview = (bool) ($submission->attachment_preview_path ?? false);
                        $streamUrl = $hasPreview
                            ? route('submissions.attachment', [$class, $assignment, $submission, 'preview' => 1], false)
                            : route('submissions.attachment', [$class, $assignment, $submission], false);
                    @endphp

                    <div class="text-sm text-gray-600">
                        {{ $mimeString !== '' ? $mimeString : 'file' }}
                    </div>

                    @if($hasPreview)
                        <iframe src="{{ $streamUrl }}" class="w-full h-[75vh] border rounded"></iframe>
                    @elseif(str_starts_with($mimeString, 'image/'))
                        <img src="{{ $streamUrl }}" alt="Attachment" class="max-h-[70vh] mx-auto" />
                    @elseif($mimeString === 'application/pdf')
                        <iframe src="{{ $streamUrl }}" class="w-full h-[75vh] border rounded"></iframe>
                    @elseif(str_starts_with($mimeString, 'audio/'))
                        <audio controls class="w-full">
                            <source src="{{ $streamUrl }}" type="{{ $mimeString }}" />
                        </audio>
                    @elseif(str_starts_with($mimeString, 'video/'))
                        <video controls class="w-full max-h-[75vh]">
                            <source src="{{ $streamUrl }}" type="{{ $mimeString }}" />
                        </video>
                    @else
                        <div class="text-sm text-gray-700">
                            Preview may be limited for this file type in the browser.
                            <div class="mt-2">
                                <a class="text-indigo-600 hover:underline" target="_blank" rel="noopener" href="{{ $streamUrl }}">Open in new tab</a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
