<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Preview: {{ $assignment->title }}</h2>
            <div class="text-sm text-gray-500">Class: {{ $class->name }} • Assignment Attachment</div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 space-y-4">
                    <div class="text-sm text-gray-600">
                        {{ $assignment->attachment_original_name ?? 'Attachment' }}
                        ({{ $assignment->attachment_mime_type ?? 'file' }})
                    </div>

                    @php
                        $mime = (string) ($assignment->attachment_mime_type ?? '');
                        $hasPreview = (bool) ($assignment->attachment_preview_path);
                        $streamUrl = $hasPreview
                            ? route('assignments.attachment', [$class, $assignment, 'preview' => 1], false)
                            : route('assignments.attachment', [$class, $assignment], false);
                    @endphp

                    @if($hasPreview)
                        <iframe src="{{ $streamUrl }}" class="w-full h-[75vh] border rounded"></iframe>
                    @elseif(str_starts_with($mime, 'image/'))
                        <img src="{{ $streamUrl }}" alt="Attachment" class="max-h-[70vh] mx-auto" />
                    @elseif($mime === 'application/pdf')
                        <iframe src="{{ $streamUrl }}" class="w-full h-[75vh] border rounded"></iframe>
                    @elseif(str_starts_with($mime, 'audio/'))
                        <audio controls class="w-full">
                            <source src="{{ $streamUrl }}" type="{{ $mime }}" />
                        </audio>
                    @elseif(str_starts_with($mime, 'video/'))
                        <video controls class="w-full max-h-[75vh]">
                            <source src="{{ $streamUrl }}" type="{{ $mime }}" />
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
