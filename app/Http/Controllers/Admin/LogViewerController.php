<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LogViewerController extends Controller
{
    /**
     * Display the Laravel log viewer and editor.
     */
    public function index(Request $request)
    {
        @ini_set('memory_limit', '256M');

        $logPath = storage_path('logs');

        // Ensure storage/logs directory exists
        if (!File::isDirectory($logPath)) {
            File::makeDirectory($logPath, 0755, true);
        }

        // Gather all .log files in the storage/logs folder
        $files = [];
        $logFiles = File::glob($logPath . '/*.log');

        foreach ($logFiles as $filePath) {
            $fileName = basename($filePath);
            $files[] = [
                'name' => $fileName,
                'path' => $filePath,
                'size' => File::size($filePath),
                'formatted_size' => $this->formatBytes(File::size($filePath)),
                'modified' => File::lastModified($filePath),
                'modified_formatted' => date('Y-m-d H:i:s', File::lastModified($filePath)),
            ];
        }

        // Sort files by modified time descending (newest first)
        usort($files, fn($a, $b) => $b['modified'] <=> $a['modified']);

        // Determine currently selected log file
        $requestedFile = $request->query('file');
        $selectedFileName = 'laravel.log';

        if ($requestedFile) {
            $cleaned = basename($requestedFile);
            if (str_ends_with($cleaned, '.log')) {
                $selectedFileName = $cleaned;
            }
        } elseif (!empty($files)) {
            // Default to laravel.log if exists, otherwise the newest log file
            $hasLaravelLog = collect($files)->firstWhere('name', 'laravel.log');
            $selectedFileName = $hasLaravelLog ? 'laravel.log' : $files[0]['name'];
        }

        $currentFilePath = $logPath . DIRECTORY_SEPARATOR . $selectedFileName;

        // If the selected file is laravel.log and does not exist yet, create an empty one
        if (!File::exists($currentFilePath) && $selectedFileName === 'laravel.log') {
            @touch($currentFilePath);
            if (File::exists($currentFilePath)) {
                $files[] = [
                    'name' => 'laravel.log',
                    'path' => $currentFilePath,
                    'size' => 0,
                    'formatted_size' => '0 B',
                    'modified' => time(),
                    'modified_formatted' => date('Y-m-d H:i:s'),
                ];
            }
        }

        $fileExists = File::exists($currentFilePath);
        $fileSize = $fileExists ? File::size($currentFilePath) : 0;
        $isWritable = $fileExists ? is_writable($currentFilePath) : is_writable($logPath);
        $lastModified = $fileExists ? File::lastModified($currentFilePath) : null;

        $rawContent = '';
        $isTruncated = false;
        $maxReadBytes = 512 * 1024; // 512 KB safe ceiling to prevent PHP memory exhaustion

        if ($fileExists) {
            if ($fileSize > $maxReadBytes) {
                $handle = fopen($currentFilePath, 'r');
                if ($handle) {
                    fseek($handle, -$maxReadBytes, SEEK_END);
                    $rawContent = fread($handle, $maxReadBytes) ?: '';
                    fclose($handle);
                    $isTruncated = true;
                }
            } else {
                $rawContent = File::get($currentFilePath);
            }
        }

        $lineCount = $rawContent !== '' ? substr_count($rawContent, "\n") + 1 : 0;
        $entries = $this->parseLogContent($rawContent);

        // Cap parsed entries at 200 most recent to prevent browser memory/DOM freeze
        if (count($entries) > 200) {
            $entries = array_slice($entries, 0, 200);
        }

        // Calculate severity stats
        $stats = [
            'total_entries' => count($entries),
            'emergency' => 0,
            'alert' => 0,
            'critical' => 0,
            'error' => 0,
            'warning' => 0,
            'notice' => 0,
            'info' => 0,
            'debug' => 0,
        ];

        foreach ($entries as $entry) {
            $lvl = strtolower($entry['level'] ?? '');
            if (isset($stats[$lvl])) {
                $stats[$lvl]++;
            }
        }

        $stats['total_errors'] = $stats['emergency'] + $stats['alert'] + $stats['critical'] + $stats['error'];
        $stats['total_warnings'] = $stats['warning'] + $stats['notice'];

        return view('admin.logs.index', [
            'files' => $files,
            'currentFile' => $selectedFileName,
            'currentFilePath' => $currentFilePath,
            'fileExists' => $fileExists,
            'isWritable' => $isWritable,
            'fileSize' => $fileSize,
            'formattedSize' => $this->formatBytes($fileSize),
            'lastModified' => $lastModified,
            'rawContent' => $rawContent,
            'isTruncated' => $isTruncated,
            'lineCount' => $lineCount,
            'entries' => $entries,
            'stats' => $stats,
        ]);
    }

    /**
     * Update log file content.
     */
    public function update(Request $request)
    {
        $fileName = basename($request->input('file', 'laravel.log'));

        if (!str_ends_with($fileName, '.log')) {
            return redirect()->back()->withErrors(['file' => 'Invalid log file format.']);
        }

        $filePath = storage_path('logs/' . $fileName);
        $content = $request->input('content', '');

        try {
            File::put($filePath, $content);

            return redirect()->route('admin.logs.index', ['file' => $fileName])
                ->with('status', "Log file '{$fileName}' saved and updated successfully.");
        } catch (\Throwable $e) {
            return redirect()->back()
                ->withErrors(['content' => 'Failed to save log file: ' . $e->getMessage()]);
        }
    }

    /**
     * Clear / empty log file content.
     */
    public function clear(Request $request)
    {
        $fileName = basename($request->input('file', 'laravel.log'));

        if (!str_ends_with($fileName, '.log')) {
            return redirect()->back()->withErrors(['file' => 'Invalid log file.']);
        }

        $filePath = storage_path('logs/' . $fileName);

        if (File::exists($filePath)) {
            try {
                File::put($filePath, '');

                return redirect()->route('admin.logs.index', ['file' => $fileName])
                    ->with('status', "Log file '{$fileName}' cleared successfully.");
            } catch (\Throwable $e) {
                return redirect()->back()
                    ->withErrors(['file' => 'Could not clear file: ' . $e->getMessage()]);
            }
        }

        return redirect()->route('admin.logs.index', ['file' => $fileName])
            ->with('status', "Log file '{$fileName}' is already empty.");
    }

    /**
     * Download log file.
     */
    public function download(Request $request): BinaryFileResponse
    {
        $fileName = basename($request->query('file', 'laravel.log'));

        if (!str_ends_with($fileName, '.log')) {
            abort(404, 'Invalid log file.');
        }

        $filePath = storage_path('logs/' . $fileName);

        if (!File::exists($filePath)) {
            abort(404, 'Log file not found.');
        }

        return response()->download($filePath, $fileName, [
            'Content-Type' => 'text/plain',
        ]);
    }

    /**
     * Write a test entry to test logging in production/local.
     */
    public function testEntry(Request $request)
    {
        $level = strtolower($request->input('level', 'info'));
        $message = $request->input('message') ?: 'Manual test log entry created from Raimart Admin Panel at ' . now()->toDateTimeString();

        switch ($level) {
            case 'emergency':
                Log::emergency($message);
                break;
            case 'alert':
                Log::alert($message);
                break;
            case 'critical':
                Log::critical($message);
                break;
            case 'error':
                Log::error($message);
                break;
            case 'warning':
                Log::warning($message);
                break;
            case 'notice':
                Log::notice($message);
                break;
            case 'debug':
                Log::debug($message);
                break;
            default:
                Log::info($message);
                break;
        }

        $fileName = basename($request->input('file', 'laravel.log'));

        return redirect()->route('admin.logs.index', ['file' => $fileName])
            ->with('status', "A test [{$level}] log entry has been written to the logs!");
    }

    /**
     * Parse log file content into structured entries.
     */
    private function parseLogContent(string $rawContent): array
    {
        if (trim($rawContent) === '') {
            return [];
        }

        // Pattern matching standard Laravel log entry headers
        // [2026-10-13 12:45:00] local.ERROR: Message...
        $pattern = '/^\[(\d{4}-\d{2}-\d{2}[ T]\d{2}:\d{2}:\d{2}(?:\.\d+)?(?:[+-]\d{2}:?\d{2})?)\]\s*(?:([a-zA-Z0-9_\-]+)\.)?([A-Za-z]+):\s*(.*?)(?=(?:^\[\d{4}-\d{2}-\d{2})|\z)/ms';

        preg_match_all($pattern, $rawContent, $matches, PREG_SET_ORDER);

        $entries = [];

        if (!empty($matches)) {
            foreach ($matches as $index => $match) {
                $timestamp = $match[1] ?? '';
                $env = $match[2] ?? 'production';
                $level = strtoupper($match[3] ?? 'INFO');
                $fullMessage = trim($match[4] ?? '');

                $stackTrace = '';
                $message = $fullMessage;

                if (preg_match('/^(.*?)(?:(\[stacktrace\]|Stack trace:|#0\s+)(.*))$/s', $fullMessage, $msgParts)) {
                    $message = trim($msgParts[1]);
                    $stackTrace = trim(($msgParts[2] ?? '') . ($msgParts[3] ?? ''));
                }

                $entries[] = [
                    'id' => $index + 1,
                    'timestamp' => $timestamp,
                    'env' => $env,
                    'level' => $level,
                    'message' => $message ?: $fullMessage,
                    'stack_trace' => $stackTrace,
                    'raw' => trim($match[0]),
                ];
            }
        } else {
            // Non-standard format (plain text lines)
            $lines = preg_split('/\r\n|\r|\n/', trim($rawContent));
            foreach ($lines as $i => $line) {
                if (trim($line) !== '') {
                    $entries[] = [
                        'id' => $i + 1,
                        'timestamp' => '-',
                        'env' => 'system',
                        'level' => 'INFO',
                        'message' => $line,
                        'stack_trace' => '',
                        'raw' => $line,
                    ];
                }
            }
        }

        // Return newest entries first
        return array_reverse($entries);
    }

    /**
     * Format bytes into human-readable string.
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min((int) $pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
