<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanupTempFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loan:cleanup-temp-files
                           {--hours=24 : Hours after which temp files are considered expired}
                           {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired temporary loan application files';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $hours = (int) $this->option('hours');
        $dryRun = $this->option('dry-run');

        $this->info("開始清理超過 {$hours} 小時的臨時檔案...");

        $disk = Storage::disk('public');
        $tempPath = 'temp';

        if (!$disk->exists($tempPath)) {
            $this->info('臨時檔案目錄不存在');
            return 0;
        }

        $directories = $disk->directories($tempPath);
        $deletedCount = 0;
        $totalSize = 0;

        $cutoffTime = Carbon::now()->subHours($hours);

        foreach ($directories as $directory) {
            try {
                // 獲取目錄的最後修改時間
                $lastModified = Carbon::createFromTimestamp(
                    $disk->lastModified($directory)
                );

                if ($lastModified->lt($cutoffTime)) {
                    // 計算目錄大小
                    $size = $this->getDirectorySize($disk, $directory);
                    $totalSize += $size;

                    if ($dryRun) {
                        $this->line("將刪除: {$directory} (大小: " . $this->formatBytes($size) . ")");
                    } else {
                        $disk->deleteDirectory($directory);
                        $this->line("已刪除: {$directory} (大小: " . $this->formatBytes($size) . ")");
                    }

                    $deletedCount++;
                }
            } catch (\Exception $e) {
                $this->error("處理目錄 {$directory} 時發生錯誤: " . $e->getMessage());
            }
        }

        if ($dryRun) {
            $this->info("模擬運行完成。將刪除 {$deletedCount} 個目錄，總大小: " . $this->formatBytes($totalSize));
        } else {
            $this->info("清理完成。已刪除 {$deletedCount} 個目錄，釋放空間: " . $this->formatBytes($totalSize));
        }

        // 記錄日誌
        \Log::info('Temporary files cleanup completed', [
            'deleted_directories' => $deletedCount,
            'total_size_freed' => $totalSize,
            'dry_run' => $dryRun,
            'hours_threshold' => $hours
        ]);

        return 0;
    }

    /**
     * 計算目錄大小
     */
    private function getDirectorySize($disk, $directory)
    {
        $size = 0;
        $files = $disk->allFiles($directory);

        foreach ($files as $file) {
            $size += $disk->size($file);
        }

        return $size;
    }

    /**
     * 格式化位元組數
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * 清理特定 session 的臨時檔案
     */
    public function cleanupSessionFiles($sessionId)
    {
        $disk = Storage::disk('public');
        $sessionPath = 'temp/' . $sessionId;

        if ($disk->exists($sessionPath)) {
            $size = $this->getDirectorySize($disk, $sessionPath);
            $disk->deleteDirectory($sessionPath);

            \Log::info('Session temporary files cleaned', [
                'session_id' => $sessionId,
                'size_freed' => $size
            ]);

            return $size;
        }

        return 0;
    }
}
