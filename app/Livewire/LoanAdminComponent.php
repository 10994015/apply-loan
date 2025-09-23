<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\LoanApplication;
use Carbon\Carbon;

class LoanAdminComponent extends Component
{
    use WithPagination;

    // 搜尋和篩選
    public $search = '';
    public $statusFilter = 'all';
    public $cityFilter = 'all';
    public $dateFilter = 'all';
    public $perPage = 10;
    public $sortField = 'applied_at';
    public $sortDirection = 'desc';

    // 統計資料
    public $stats = [];

    // 詳細資料模態框
    public $showingDetail = false;
    public $selectedApplication = null;

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

    public function mount()
    {
        $this->loadStats();
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
            'total_amount' => LoanApplication::sum('amount'),
            'avg_amount' => LoanApplication::avg('amount'),
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
            $application->update(['status' => $status]);
            $this->loadStats(); // 重新載入統計

            // 如果正在顯示詳細資料，更新選中的申請
            if ($this->selectedApplication && $this->selectedApplication->id === $applicationId) {
                $this->selectedApplication = $application->fresh();
            }

            session()->flash('message', "申請編號 #{$applicationId} 狀態已更新為：{$application->status_name}");
        }
    }

    public function deleteApplication($applicationId)
    {
        $application = LoanApplication::find($applicationId);

        if ($application) {
            // 如果正在顯示要刪除的申請，關閉詳細資料模態框
            if ($this->selectedApplication && $this->selectedApplication->id === $applicationId) {
                $this->closeDetail();
            }

            $application->delete();
            $this->loadStats(); // 重新載入統計

            session()->flash('message', "申請編號 #{$applicationId} 已刪除");
        }
    }

    public function showDetail($applicationId)
    {
        $this->selectedApplication = LoanApplication::find($applicationId);
        $this->showingDetail = true;
    }

    public function closeDetail()
    {
        $this->showingDetail = false;
        $this->selectedApplication = null;
    }

    public function exportToday()
    {
        // 這裡可以實作匯出功能
        session()->flash('message', '今日資料匯出功能開發中...');
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
     * 獲取統計資料按城市分組
     */
    public function getCityStats()
    {
        return LoanApplication::select('city')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(amount) as total_amount')
            ->selectRaw('AVG(amount) as avg_amount')
            ->groupBy('city')
            ->orderByDesc('total')
            ->limit(10)
            ->get();
    }

    /**
     * 獲取最近的申請活動
     */
    public function getRecentActivity()
    {
        return LoanApplication::latest('applied_at')
            ->limit(5)
            ->get();
    }

    /**
     * 批量更新狀態
     */
    public function bulkUpdateStatus($applicationIds, $status)
    {
        $updated = LoanApplication::whereIn('id', $applicationIds)
            ->update(['status' => $status]);

        $this->loadStats();

        session()->flash('message', "已批量更新 {$updated} 筆申請狀態");
    }

    /**
     * 批量刪除
     */
    public function bulkDelete($applicationIds)
    {
        $deleted = LoanApplication::whereIn('id', $applicationIds)->delete();

        $this->loadStats();

        session()->flash('message', "已批量刪除 {$deleted} 筆申請");
    }

    public function getApplicationsProperty()
    {
        $query = LoanApplication::query();

        // 搜尋 - 擴展搜尋欄位包含新的欄位
        if ($this->search) {
            $query->where(function($q) {
                $q->where('phone', 'like', '%' . $this->search . '%')
                  ->orWhere('id', 'like', '%' . $this->search . '%')
                  ->orWhere('name', 'like', '%' . $this->search . '%')
                  ->orWhere('occupation', 'like', '%' . $this->search . '%')
                  ->orWhere('city', 'like', '%' . $this->search . '%')
                  ->orWhere('line_id', 'like', '%' . $this->search . '%');
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
     * 匯出資料為 CSV
     */
    public function exportCsv($dateRange = 'all')
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

        $filename = 'loan_applications_' . Carbon::now()->format('Y_m_d_H_i_s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($applications) {
            $file = fopen('php://output', 'w');

            // 加入 BOM 讓 Excel 正確顯示中文
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // CSV 標題列
            fputcsv($file, [
                '申請編號',
                '姓名',
                '手機號碼',
                '職業',
                '居住縣市',
                '方便聯繫時間',
                'Line ID',
                '申請金額',
                '狀態',
                '申請時間',
                '備註'
            ]);

            // 資料列
            foreach ($applications as $app) {
                fputcsv($file, [
                    str_pad($app->id, 6, '0', STR_PAD_LEFT),
                    $app->name,
                    $app->formatted_phone,
                    $app->occupation,
                    $app->city,
                    $app->contact_time,
                    $app->line_id ?: '',
                    number_format($app->amount, 0),
                    $app->status_name,
                    $app->formatted_applied_at,
                    $app->notes ?: ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * 獲取申請趨勢資料 (最近30天)
     */
    public function getApplicationTrend()
    {
        $days = collect();

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = LoanApplication::whereDate('applied_at', $date)->count();

            $days->push([
                'date' => $date->format('m/d'),
                'count' => $count
            ]);
        }

        return $days;
    }

    /**
     * 獲取狀態分布統計
     */
    public function getStatusDistribution()
    {
        return LoanApplication::select('status')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM loan_applications)) as percentage')
            ->groupBy('status')
            ->get()
            ->map(function($item) {
                return [
                    'status' => $item->status,
                    'status_name' => LoanApplication::getStatusOptions()[$item->status] ?? $item->status,
                    'count' => $item->count,
                    'percentage' => round($item->percentage, 1)
                ];
            });
    }

    /**
     * 獲取金額區間分布
     */
    public function getAmountDistribution()
    {
        return [
            [
                'range' => '7,000-20,000',
                'count' => LoanApplication::whereBetween('amount', [7000, 20000])->count()
            ],
            [
                'range' => '20,001-50,000',
                'count' => LoanApplication::whereBetween('amount', [20001, 50000])->count()
            ],
            [
                'range' => '50,001-80,000',
                'count' => LoanApplication::whereBetween('amount', [50001, 80000])->count()
            ],
            [
                'range' => '80,001-100,000',
                'count' => LoanApplication::whereBetween('amount', [80001, 100000])->count()
            ]
        ];
    }

    public function render()
    {
        return view('livewire.loan-admin-component', [
            'applications' => $this->applications,
            'stats' => $this->stats,
            'cityOptions' => $this->getCityOptions(),
            'cityStats' => $this->getCityStats(),
            'recentActivity' => $this->getRecentActivity(),
            'applicationTrend' => $this->getApplicationTrend(),
            'statusDistribution' => $this->getStatusDistribution(),
            'amountDistribution' => $this->getAmountDistribution(),
        ])->layout('layouts.admin', ['title' => '貸款申請管理']);
    }
}
