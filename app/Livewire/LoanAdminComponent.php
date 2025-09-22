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
    public $dateFilter = 'all';
    public $perPage = 10;
    public $sortField = 'applied_at';
    public $sortDirection = 'desc';

    // 統計資料
    public $stats = [];

    // 重置分頁當搜尋條件改變時
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
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

            session()->flash('message', "申請編號 #{$applicationId} 狀態已更新為：{$application->status_name}");
        }
    }

    public function deleteApplication($applicationId)
    {
        $application = LoanApplication::find($applicationId);

        if ($application) {
            $application->delete();
            $this->loadStats(); // 重新載入統計

            session()->flash('message', "申請編號 #{$applicationId} 已刪除");
        }
    }

    public function exportToday()
    {
        // 這裡可以實作匯出功能
        session()->flash('message', '今日資料匯出功能開發中...');
    }

    public function getApplicationsProperty()
    {
        $query = LoanApplication::query();

        // 搜尋
        if ($this->search) {
            $query->where(function($q) {
                $q->where('phone', 'like', '%' . $this->search . '%')
                  ->orWhere('id', 'like', '%' . $this->search . '%');
            });
        }

        // 狀態篩選
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
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

    public function render()
    {
        return view('livewire.loan-admin-component', [
            'applications' => $this->applications,
            'stats' => $this->stats
        ])->layout('layouts.admin', ['title' => '貸款申請管理']);
    }
}
