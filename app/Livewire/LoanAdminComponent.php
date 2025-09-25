<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\LoanApplication;
use App\Exports\LoanApplicationsExport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class LoanAdminComponent extends Component
{
    use WithPagination;

    // 搜尋和篩選
    public $search = '';
    public $statusFilter = 'all';
    public $cityFilter = 'all';
    public $dateFilter = 'all';
    public $stepFilter = 'all';
    public $perPage = 10;
    public $sortField = 'applied_at';
    public $sortDirection = 'desc';

    // 統計資料
    public $stats = [];

    // 詳細資料模態框
    public $showingDetail = false;
    public $selectedApplication = null;
    public $showingDocuments = false;

    // 批量操作
    public $selectedApplications = [];
    public $selectAll = false;

    // 重置分頁當搜尋條件改變時
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingCityFilter()
    {
        $this->resetPage();
    }

    public function updatingDateFilter()
    {
        $this->resetPage();
    }

    public function updatingStepFilter()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->loadStats();
    }

    public function hydrate()
    {
        if (empty($this->stats)) {
            $this->loadStats();
        }
    }

    public function loadStats()
    {
        $this->stats = [
            'total' => LoanApplication::count(),
            'today' => LoanApplication::whereDate('applied_at', Carbon::today())->count(),
            'pending' => LoanApplication::where('status', 'pending')->count(),
            'approved' => LoanApplication::where('status', 'approved')->count(),
            'rejected' => LoanApplication::where('status', 'rejected')->count(),
            'processing' => LoanApplication::where('status', 'processing')->count(),
            'total_amount' => LoanApplication::sum('amount') ?? 0,
            'avg_amount' => LoanApplication::avg('amount') ?? 0,
            'incomplete' => LoanApplication::where('step_5_completed', false)->count(),
            'complete' => LoanApplication::where('step_5_completed', true)->count(),
        ];
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function updateStatus($applicationId, $status)
    {
        $application = LoanApplication::find($applicationId);

        if ($application) {
            $application->update([
                'status' => $status,
                'updated_at' => now()
            ]);
            $this->loadStats();

            // 如果正在顯示詳細資料，更新選中的申請
            if ($this->selectedApplication && $this->selectedApplication->id === $applicationId) {
                $this->selectedApplication = $application->fresh();
            }

            session()->flash('message', "申請編號 #{$applicationId} 狀態已更新為：{$this->getStatusName($status)}");
        }
    }

    public function deleteApplication($applicationId)
    {
        $application = LoanApplication::find($applicationId);

        if ($application) {
            // 刪除相關檔案
            $this->deleteApplicationFiles($application);

            // 如果正在顯示要刪除的申請，關閉詳細資料模態框
            if ($this->selectedApplication && $this->selectedApplication->id === $applicationId) {
                $this->closeDetail();
            }

            $application->delete();
            $this->loadStats();

            session()->flash('message', "申請編號 #{$applicationId} 已刪除");
        }
    }

    private function deleteApplicationFiles($application)
    {
        $filePaths = [
            $application->id_card_front_path,
            $application->id_card_back_path,
            $application->id_card_selfie_path,
            $application->second_document_path,
            $application->bank_card_path,
        ];

        foreach ($filePaths as $path) {
            if ($path) {
                try {
                    // 使用 S3 disk 刪除檔案
                    if (Storage::disk('s3')->exists($path)) {
                        Storage::disk('s3')->delete($path);
                        \Log::info('S3 file deleted successfully', ['path' => $path]);
                    } else {
                        \Log::warning('S3 file not found for deletion', ['path' => $path]);
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to delete S3 file', [
                        'path' => $path,
                        'error' => $e->getMessage()
                    ]);
                    // 繼續處理其他檔案，不中斷流程
                }
            }
        }
    }
    // 如果需要批量刪除整個目錄（可選）
    private function deleteApplicationDirectory($applicationId)
    {
        try {
            $directoryPath = "loan_documents/{$applicationId}";

            // 列出目錄下的所有檔案
            $files = Storage::disk('s3')->allFiles($directoryPath);

            if (!empty($files)) {
                // 刪除所有檔案
                Storage::disk('s3')->delete($files);
                \Log::info('S3 application directory deleted', [
                    'directory' => $directoryPath,
                    'files_count' => count($files)
                ]);
            }

            // 嘗試刪除空目錄（S3 可能不需要這步驟）
            try {
                Storage::disk('s3')->deleteDirectory($directoryPath);
            } catch (\Exception $e) {
                // 空目錄刪除失敗不是致命錯誤
                \Log::info('S3 directory deletion skipped', ['error' => $e->getMessage()]);
            }

        } catch (\Exception $e) {
            \Log::error('Failed to delete S3 application directory', [
                'application_id' => $applicationId,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function showDetail($applicationId)
    {
        $this->selectedApplication = LoanApplication::find($applicationId);
        $this->showingDetail = true;
        $this->showingDocuments = false;
    }

    public function closeDetail()
    {
        $this->showingDetail = false;
        $this->showingDocuments = false;
        $this->selectedApplication = null;
    }

    public function toggleDocuments()
    {
        $this->showingDocuments = !$this->showingDocuments;
    }

    public function addNote($applicationId, $note)
    {
        $application = LoanApplication::find($applicationId);
        if ($application) {
            $currentNotes = $application->notes ? $application->notes . "\n" : '';
            $timestamp = Carbon::now()->format('Y-m-d H:i:s');
            $newNote = "[{$timestamp}] {$note}";

            $application->update([
                'notes' => $currentNotes . $newNote
            ]);

            if ($this->selectedApplication && $this->selectedApplication->id === $applicationId) {
                $this->selectedApplication = $application->fresh();
            }
        }
    }

    // 批量操作
    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedApplications = $this->applications->pluck('id')->toArray();
        } else {
            $this->selectedApplications = [];
        }
    }

    public function bulkUpdateStatus($status)
    {
        if (empty($this->selectedApplications)) {
            session()->flash('error', '請先選擇要更新的申請');
            return;
        }

        $updated = LoanApplication::whereIn('id', $this->selectedApplications)
            ->update(['status' => $status]);

        $this->loadStats();
        $this->selectedApplications = [];
        $this->selectAll = false;

        session()->flash('message', "已批量更新 {$updated} 筆申請狀態為：{$this->getStatusName($status)}");
    }

    public function bulkDelete()
    {
        if (empty($this->selectedApplications)) {
            session()->flash('error', '請先選擇要刪除的申請');
            return;
        }

        // 刪除相關檔案
        $applications = LoanApplication::whereIn('id', $this->selectedApplications)->get();
        foreach ($applications as $application) {
            $this->deleteApplicationFiles($application);
        }

        $deleted = LoanApplication::whereIn('id', $this->selectedApplications)->delete();

        $this->loadStats();
        $this->selectedApplications = [];
        $this->selectAll = false;

        session()->flash('message', "已批量刪除 {$deleted} 筆申請");
    }

    private function getStatusName($status)
    {
        $statusNames = [
            'pending' => '待審核',
            'processing' => '處理中',
            'approved' => '已核准',
            'rejected' => '已拒絕'
        ];

        return $statusNames[$status] ?? $status;
    }

    /**
     * 獲取縣市選項
     */
    public function getCityOptions()
    {
        return LoanApplication::select('city')
            ->distinct()
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->orderBy('city')
            ->pluck('city')
            ->toArray();
    }

    /**
     * 獲取步驟進度統計
     */
    public function getStepStats()
    {
        return [
            'step_1' => LoanApplication::where('step_1_completed', true)->count(),
            'step_2' => LoanApplication::where('step_2_completed', true)->count(),
            'step_3' => LoanApplication::where('step_3_completed', true)->count(),
            'step_4' => LoanApplication::where('step_4_completed', true)->count(),
            'step_5' => LoanApplication::where('step_5_completed', true)->count(),
        ];
    }

    public function getApplicationsProperty()
    {
        $query = LoanApplication::query();

        // 搜尋 - 擴展搜尋欄位包含所有相關欄位
        if ($this->search) {
            $query->where(function($q) {
                $q->where('phone', 'like', '%' . $this->search . '%')
                  ->orWhere('id', 'like', '%' . $this->search . '%')
                  ->orWhere('name', 'like', '%' . $this->search . '%')
                  ->orWhere('occupation', 'like', '%' . $this->search . '%')
                  ->orWhere('city', 'like', '%' . $this->search . '%')
                  ->orWhere('address', 'like', '%' . $this->search . '%')
                  ->orWhere('line_id', 'like', '%' . $this->search . '%')
                  ->orWhere('emergency_contact_1_name', 'like', '%' . $this->search . '%')
                  ->orWhere('emergency_contact_2_name', 'like', '%' . $this->search . '%');
            });
        }

        // 狀態篩選
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // 縣市篩選
        if ($this->cityFilter !== 'all') {
            $query->where('city', $this->cityFilter);
        }

        // 步驟篩選
        if ($this->stepFilter !== 'all') {
            switch ($this->stepFilter) {
                case 'incomplete':
                    $query->where('step_5_completed', false);
                    break;
                case 'complete':
                    $query->where('step_5_completed', true);
                    break;
                default:
                    if (is_numeric($this->stepFilter)) {
                        $query->where('current_step', $this->stepFilter);
                    }
                    break;
            }
        }

        // 日期篩選
        switch ($this->dateFilter) {
            case 'today':
                $query->whereDate('applied_at', Carbon::today());
                break;
            case 'yesterday':
                $query->whereDate('applied_at', Carbon::yesterday());
                break;
            case 'this_week':
                $query->whereBetween('applied_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ]);
                break;
            case 'this_month':
                $query->whereMonth('applied_at', Carbon::now()->month)
                      ->whereYear('applied_at', Carbon::now()->year);
                break;
        }

        // 排序
        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    /**
     * 匯出資料為 Excel
     */
    public function exportExcel($dateRange = 'all')
    {
        $query = LoanApplication::query();

        switch ($dateRange) {
            case 'today':
                $query->whereDate('applied_at', Carbon::today());
                break;
            case 'this_week':
                $query->whereBetween('applied_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ]);
                break;
            case 'this_month':
                $query->whereMonth('applied_at', Carbon::now()->month)
                      ->whereYear('applied_at', Carbon::now()->year);
                break;
        }

        $applications = $query->orderBy('applied_at', 'desc')->get();

        $filename = 'loan_applications_' . Carbon::now()->format('Y_m_d_H_i_s') . '.xlsx';

        // 準備 Excel 資料
        $excelData = [];

        // 標題列
        $excelData[] = [
            '申請編號', '姓名', '手機號碼', '職業', '居住縣市', '詳細地址',
            '方便聯繫時間', 'Line ID', '申請金額', '狀態', '當前步驟',
            '緊急聯絡人1姓名', '緊急聯絡人1電話', '緊急聯絡人1關係',
            '緊急聯絡人2姓名', '緊急聯絡人2電話', '緊急聯絡人2關係',
            '申請時間', '步驟1完成時間', '步驟2完成時間', '步驟3完成時間',
            '步驟4完成時間', '步驟5完成時間', '備註'
        ];

        // 資料列
        foreach ($applications as $app) {
            $excelData[] = [
                str_pad($app->id, 6, '0', STR_PAD_LEFT),
                $app->name,
                $app->phone,
                $app->occupation,
                $app->city,
                $app->address,
                $app->contact_time,
                $app->line_id ?: '',
                $app->amount,
                $this->getStatusName($app->status),
                $app->current_step,
                $app->emergency_contact_1_name ?: '',
                $app->emergency_contact_1_phone ?: '',
                $app->emergency_contact_1_relationship ?: '',
                $app->emergency_contact_2_name ?: '',
                $app->emergency_contact_2_phone ?: '',
                $app->emergency_contact_2_relationship ?: '',
                $app->applied_at->format('Y-m-d H:i:s'),
                $app->step_1_completed_at ? $app->step_1_completed_at->format('Y-m-d H:i:s') : '',
                $app->step_2_completed_at ? $app->step_2_completed_at->format('Y-m-d H:i:s') : '',
                $app->step_3_completed_at ? $app->step_3_completed_at->format('Y-m-d H:i:s') : '',
                $app->step_4_completed_at ? $app->step_4_completed_at->format('Y-m-d H:i:s') : '',
                $app->step_5_completed_at ? $app->step_5_completed_at->format('Y-m-d H:i:s') : '',
                $app->notes ?: ''
            ];
        }

        // 使用 Laravel Excel 匯出
        return Excel::download(
            new LoanApplicationsExport($excelData),
            $filename
        );
    }

    public function getStatsProperty()
    {
        if (empty($this->stats)) {
            $this->loadStats();
        }
        return $this->stats;
    }
    public function getS3FileUrl($filePath)
    {
        if (!$filePath) {
            return null;
        }

        try {
            // 檢查檔案是否存在
            if (!Storage::disk('s3')->exists($filePath)) {
                \Log::warning('S3 file not found', ['path' => $filePath]);
                return null;
            }

            // 使用臨時 URL（1小時有效期）
            return Storage::disk('s3')->temporaryUrl($filePath, now()->addHours(1));

        } catch (\Exception $e) {
            \Log::error('Failed to get S3 temporary URL', [
                'file_path' => $filePath,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    // 或者，如果你想要公開訪問，需要設置 Bucket 為公開
    /**
     * 獲取 S3 公開 URL（需要 Bucket 為公開）
     */
    public function getS3PublicUrl($filePath)
    {
        if (!$filePath) {
            return null;
        }

        try {
            // 構建公開 URL
            $bucket = config('filesystems.disks.s3.bucket');
            $region = config('filesystems.disks.s3.region');

            // 標準 S3 URL 格式
            return "https://{$bucket}.s3.{$region}.amazonaws.com/{$filePath}";

        } catch (\Exception $e) {
            \Log::error('Failed to build S3 public URL', [
                'file_path' => $filePath,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    // 智能選擇方法
    public function getS3FileUrlSmart($filePath)
    {
        if (!$filePath) {
            return null;
        }

        try {
            // 先嘗試公開 URL
            $publicUrl = $this->getS3PublicUrl($filePath);

            // 如果需要權限驗證，回退到臨時 URL
            if ($this->needsTemporaryUrl()) {
                return Storage::disk('s3')->temporaryUrl($filePath, now()->addHours(1));
            }

            return $publicUrl;

        } catch (\Exception $e) {
            \Log::error('Failed to get S3 file URL', [
                'file_path' => $filePath,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    private function needsTemporaryUrl()
    {
        // 檢查是否需要使用臨時 URL
        return config('filesystems.disks.s3.visibility', 'private') === 'private' ||
            env('AWS_S3_FORCE_TEMPORARY_URL', true);
    }


    /**
     * 檢查 S3 檔案是否存在
     */
    public function s3FileExists($filePath)
    {
        if (!$filePath) {
            return false;
        }

        try {
            return Storage::disk('s3')->exists($filePath);
        } catch (\Exception $e) {
            \Log::error('Failed to check S3 file existence', [
                'file_path' => $filePath,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * 獲取 S3 檔案的臨時 URL（如果需要私人訪問）
     */
    public function getS3TemporaryUrl($filePath, $minutes = 60)
    {
        if (!$filePath) {
            return null;
        }

        try {
            // 如果你的 S3 bucket 是私人的，使用臨時 URL
            return Storage::disk('s3')->temporaryUrl($filePath, now()->addMinutes($minutes));
        } catch (\Exception $e) {
            \Log::error('Failed to get S3 temporary URL', [
                'file_path' => $filePath,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }


    public function render()
    {
        return view('livewire.loan-admin-component', [
            'applications' => $this->applications,
            'stats' => $this->getStatsProperty(), // 使用屬性訪問器
            'cityOptions' => $this->getCityOptions(),
            'stepStats' => $this->getStepStats(),
        ])->layout('layouts.admin', ['title' => '貸款申請管理']);
    }

}
