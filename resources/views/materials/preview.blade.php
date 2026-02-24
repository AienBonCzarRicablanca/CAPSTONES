<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Preview: {{ $material->title }}</h2>
            <div class="text-sm text-gray-500">Class: {{ $class->name }}</div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 space-y-4">
                    <div class="text-sm text-gray-600">{{ $material->original_name }} ({{ $material->mime_type ?? 'file' }})</div>

                    @php
                        $mime = (string) ($material->mime_type ?? '');
                        $hasPreview = (bool) ($material->preview_path);
                        $streamUrl = $hasPreview
                            ? route('materials.stream', [$material, 'preview' => 1], false)
                            : route('materials.stream', $material, false);
                    @endphp

                    @if($hasPreview)
                        <iframe src="{{ $streamUrl }}" class="w-full h-[75vh] border rounded"></iframe>
                    @elseif(str_starts_with($mime, 'image/'))
                        <img src="{{ $streamUrl }}" alt="{{ $material->title }}" class="max-h-[70vh] mx-auto" />
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
                                <a class="text-indigo-600 hover:underline" target="_blank" href="{{ $streamUrl }}">Open in new tab</a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
