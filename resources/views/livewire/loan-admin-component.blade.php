<div class="admin-container">
    <!-- Header -->
    <div class="admin-header">
        <div class="header-content">
            <div class="header-left">
                <h1 class="page-title">
                    <i class="fas fa-chart-line"></i>
                    貸款申請管理
                </h1>
                <p class="page-subtitle">管理和追蹤所有貸款申請</p>
            </div>
            <div class="header-actions">
                {{-- <button wire:click="exportToday" class="btn btn-outline">
                    <i class="fas fa-download"></i>
                    匯出今日資料
                </button> --}}
                <button wire:click="$refresh" class="btn btn-primary">
                    <i class="fas fa-sync-alt"></i>
                    重新整理
                </button>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('message') }}
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card stat-card--primary">
            <div class="stat-icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ number_format($this->stats['total']) }}</div>
                <div class="stat-label">總申請數</div>
            </div>
        </div>

        <div class="stat-card stat-card--info">
            <div class="stat-icon">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ number_format($this->stats['today']) }}</div>
                <div class="stat-label">今日申請</div>
            </div>
        </div>

        <div class="stat-card stat-card--warning">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ number_format($this->stats['pending']) }}</div>
                <div class="stat-label">待審核</div>
            </div>
        </div>

        <div class="stat-card stat-card--success">
            <div class="stat-icon">
                <i class="fas fa-check"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ number_format($this->stats['approved']) }}</div>
                <div class="stat-label">已核准</div>
            </div>
        </div>

        <div class="stat-card stat-card--danger">
            <div class="stat-icon">
                <i class="fas fa-times"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ number_format($this->stats['rejected']) }}</div>
                <div class="stat-label">已拒絕</div>
            </div>
        </div>

        <div class="stat-card stat-card--money">
            <div class="stat-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">${{ number_format($this->stats['total_amount']) }}</div>
                <div class="stat-label">總申請金額</div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="filters-section">
        <div class="filters-content">
            <!-- Search -->
            <div class="filter-group">
                <label class="filter-label">搜尋</label>
                <div class="search-input-group">
                    <i class="fas fa-search"></i>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="搜尋手機號碼或申請編號..."
                        class="search-input"
                    >
                </div>
            </div>

            <!-- Status Filter -->
            <div class="filter-group">
                <label class="filter-label">狀態</label>
                <select wire:model.live="statusFilter" class="filter-select">
                    <option value="all">全部狀態</option>
                    <option value="pending">待審核</option>
                    <option value="processing">處理中</option>
                    <option value="approved">已核准</option>
                    <option value="rejected">已拒絕</option>
                </select>
            </div>

            <!-- Date Filter -->
            <div class="filter-group">
                <label class="filter-label">日期</label>
                <select wire:model.live="dateFilter" class="filter-select">
                    <option value="all">全部日期</option>
                    <option value="today">今日</option>
                    <option value="yesterday">昨日</option>
                    <option value="this_week">本週</option>
                    <option value="this_month">本月</option>
                </select>
            </div>

            <!-- Per Page -->
            <div class="filter-group">
                <label class="filter-label">每頁顯示</label>
                <select wire:model.live="perPage" class="filter-select">
                    <option value="10">10 筆</option>
                    <option value="25">25 筆</option>
                    <option value="50">50 筆</option>
                    <option value="100">100 筆</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="table-container">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>
                            <button wire:click="sortBy('id')" class="sort-button">
                                申請編號
                                @if($sortField === 'id')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fas fa-sort"></i>
                                @endif
                            </button>
                        </th>
                        <th>
                            <button wire:click="sortBy('phone')" class="sort-button">
                                手機號碼
                                @if($sortField === 'phone')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fas fa-sort"></i>
                                @endif
                            </button>
                        </th>
                        <th>
                            <button wire:click="sortBy('amount')" class="sort-button">
                                申請金額
                                @if($sortField === 'amount')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fas fa-sort"></i>
                                @endif
                            </button>
                        </th>
                        <th>
                            <button wire:click="sortBy('status')" class="sort-button">
                                狀態
                                @if($sortField === 'status')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fas fa-sort"></i>
                                @endif
                            </button>
                        </th>
                        <th>
                            <button wire:click="sortBy('applied_at')" class="sort-button">
                                申請時間
                                @if($sortField === 'applied_at')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fas fa-sort"></i>
                                @endif
                            </button>
                        </th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $application)
                        <tr class="table-row">
                            <td class="cell-id">
                                <span class="id-badge">
                                    #{{ str_pad($application->id, 6, '0', STR_PAD_LEFT) }}
                                </span>
                            </td>
                            <td class="cell-phone">
                                <div class="phone-info">
                                    <span class="phone-number">{{ $application->formatted_phone }}</span>
                                </div>
                            </td>
                            <td class="cell-amount">
                                <span class="amount-value">${{ number_format($application->amount, 0) }}</span>
                            </td>
                            <td class="cell-status">
                                <span class="status-badge status-{{ $application->status }}">
                                    {{ $application->status_name }}
                                </span>
                            </td>
                            <td class="cell-date">
                                <div class="date-info">
                                    <span class="date-primary">{{ $application->applied_at->format('Y/m/d') }}</span>
                                    <span class="date-secondary">{{ $application->applied_at->format('H:i') }}</span>
                                </div>
                            </td>
                            <td class="cell-actions">
                                <div class="action-buttons">
                                    <!-- Status Update Dropdown -->
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline dropdown-toggle">
                                            <i class="fas fa-edit"></i>
                                            狀態
                                        </button>
                                        <div class="dropdown-menu">
                                            <button wire:click="updateStatus({{ $application->id }}, 'pending')"
                                                    class="dropdown-item">
                                                <i class="fas fa-clock"></i> 待審核
                                            </button>
                                            <button wire:click="updateStatus({{ $application->id }}, 'processing')"
                                                    class="dropdown-item">
                                                <i class="fas fa-spinner"></i> 處理中
                                            </button>
                                            <button wire:click="updateStatus({{ $application->id }}, 'approved')"
                                                    class="dropdown-item">
                                                <i class="fas fa-check"></i> 核准
                                            </button>
                                            <button wire:click="updateStatus({{ $application->id }}, 'rejected')"
                                                    class="dropdown-item">
                                                <i class="fas fa-times"></i> 拒絕
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Delete Button -->
                                    <button
                                        wire:click="deleteApplication({{ $application->id }})"
                                        onclick="return confirm('確定要刪除此申請嗎？')"
                                        class="btn btn-sm btn-danger"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-state">
                                <div class="empty-content">
                                    <i class="fas fa-inbox"></i>
                                    <h3>沒有找到申請資料</h3>
                                    <p>目前沒有符合條件的貸款申請</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination-wrapper">
            {{ $applications->links() }}
        </div>
    </div>

    <!-- Loading Overlay -->
    <div wire:loading class="loading-overlay">
        <div class="loading-content">
            <i class="fas fa-spinner fa-spin"></i>
            <span>載入中...</span>
        </div>
    </div>
</div>
