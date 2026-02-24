<?php

namespace App\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class LibreOfficePreview
{
    /**
     * Converts Office documents to PDF preview using headless LibreOffice.
     * Returns the public-disk relative path to the preview PDF, or null if not supported/failed.
     */
    public static function makePdfPreviewIfSupported(string $publicDiskPath): ?string
    {
        $extension = strtolower((string) pathinfo($publicDiskPath, PATHINFO_EXTENSION));
        $supported = ['doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'odt', 'odp', 'ods'];
        if (!in_array($extension, $supported, true)) {
            return null;
        }

        $soffice = self::resolveSofficePath();
        if (!$soffice) {
            return null;
        }

        $sourceFullPath = Storage::disk('public')->path($publicDiskPath);
        if (!File::exists($sourceFullPath)) {
            return null;
        }

        $workId = (string) Str::uuid();
        $outDir = storage_path('app/lo-preview/' . $workId);
        $profileDir = storage_path('app/lo-profile/' . $workId);
        File::ensureDirectoryExists($outDir);
        File::ensureDirectoryExists($profileDir);

        try {
            $process = new Process([
                $soffice,
                '-env:UserInstallation=' . self::toFileUrl($profileDir),
                '--headless',
                '--nologo',
                '--nofirststartwizard',
                '--norestore',
                '--convert-to',
                'pdf',
                '--outdir',
                $outDir,
                $sourceFullPath,
            ]);
            $process->setTimeout(90);
            $process->run();

            if (!$process->isSuccessful()) {
                Log::warning('LibreOffice conversion failed', [
                    'path' => $publicDiskPath,
                    'exit_code' => $process->getExitCode(),
                    'stdout' => trim((string) $process->getOutput()),
                    'stderr' => trim((string) $process->getErrorOutput()),
                ]);
                return null;
            }

            $pdfFiles = File::glob($outDir . '/*.pdf');
            $pdfPath = $pdfFiles[0] ?? null;
            if (!$pdfPath || !File::exists($pdfPath)) {
                Log::warning('LibreOffice conversion produced no PDF', [
                    'path' => $publicDiskPath,
                    'stdout' => trim((string) $process->getOutput()),
                    'stderr' => trim((string) $process->getErrorOutput()),
                ]);
                return null;
            }

            $previewRelative = 'previews/' . $workId . '.pdf';
            Storage::disk('public')->put($previewRelative, File::get($pdfPath));

            return $previewRelative;
        } catch (\Throwable $e) {
            Log::warning('LibreOffice conversion exception', [
                'path' => $publicDiskPath,
                'error' => $e->getMessage(),
            ]);
            return null;
        } finally {
            try {
                File::deleteDirectory($outDir);
            } catch (\Throwable $e) {
                // ignore cleanup failures
            }

            try {
                File::deleteDirectory($profileDir);
            } catch (\Throwable $e) {
                // ignore cleanup failures
            }
        }
    }

    private static function toFileUrl(string $path): string
    {
        $normalized = str_replace('\\', '/', $path);

        // Windows absolute paths need file:///C:/...
        if (preg_match('/^[A-Za-z]:\//', $normalized) === 1) {
            return 'file:///' . $normalized;
        }

        return 'file://' . (str_starts_with($normalized, '/') ? '' : '/') . $normalized;
    }

    private static function resolveSofficePath(): ?string
    {
        $configured = (string) env('LIBREOFFICE_PATH', '');
        if ($configured !== '' && File::exists($configured)) {
            return $configured;
        }

        $candidates = [
            'C:\\Program Files\\LibreOffice\\program\\soffice.exe',
            'C:\\Program Files (x86)\\LibreOffice\\program\\soffice.exe',
        ];

        foreach ($candidates as $path) {
            if (File::exists($path)) {
                return $path;
            }
        }

        return null;
    }
}
