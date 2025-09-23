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
                        placeholder="搜尋姓名、手機號碼、職業、城市或申請編號..."
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

            <!-- City Filter -->
            <div class="filter-group">
                <label class="filter-label">縣市</label>
                <select wire:model.live="cityFilter" class="filter-select">
                    <option value="all">全部縣市</option>
                    @foreach($cityOptions as $city)
                        <option value="{{ $city }}">{{ $city }}</option>
                    @endforeach
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
                            <button wire:click="sortBy('name')" class="sort-button">
                                申請人資料
                                @if($sortField === 'name')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fas fa-sort"></i>
                                @endif
                            </button>
                        </th>
                        <th>
                            <button wire:click="sortBy('phone')" class="sort-button">
                                聯絡資訊
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
                            <td class="cell-applicant">
                                <div class="applicant-info">
                                    <div class="applicant-name">{{ $application->name }}</div>
                                    <div class="applicant-details">
                                        <small class="text-muted">
                                            {{ $application->occupation }} | {{ $application->city }}
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td class="cell-contact">
                                <div class="contact-info">
                                    <div class="phone-number">
                                        <i class="fas fa-phone"></i>
                                        {{ $application->formatted_phone }}
                                    </div>
                                    @if($application->line_id)
                                        <div class="line-id">
                                            <i class="fab fa-line"></i>
                                            {{ $application->line_id }}
                                        </div>
                                    @endif
                                    <div class="contact-time">
                                        <i class="fas fa-clock"></i>
                                        <small class="text-muted">{{ $application->contact_time }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="cell-amount">
                                <span class="amount-value">${{ number_format($application->amount, 0) }}</span>
                            </td>
                            <td class="cell-status">
                                <span class="status-badge status-{{ $application->status }}">
                                    <i class="{{ $application->status_icon }}"></i>
                                    {{ $application->status_name }}
                                </span>
                            </td>
                            <td class="cell-date">
                                <div class="date-info">
                                    <span class="date-primary">{{ $application->applied_at->format('Y/m/d') }}</span>
                                    <span class="date-secondary">{{ $application->applied_at->format('H:i') }}</span>
                                    <span class="date-diff">{{ $application->applied_at_diff }}</span>
                                </div>
                            </td>
                            <td class="cell-actions">
                                <div class="action-buttons">
                                    <!-- View Detail Button -->
                                    <button
                                        wire:click="showDetail({{ $application->id }})"
                                        class="btn btn-sm btn-info"
                                        title="查看詳細資料"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </button>

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
                                        title="刪除申請"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-state">
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

    <!-- Application Detail Modal -->
    @if($showingDetail)
        <div class="modal-overlay" wire:click="closeDetail">
            <div class="modal-content" wire:click.stop>
                <div class="modal-header">
                    <h2>申請詳細資料</h2>
                    <button wire:click="closeDetail" class="modal-close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    @if($selectedApplication)
                        <div class="detail-grid">
                            <div class="detail-section">
                                <h3>基本資料</h3>
                                <div class="detail-item">
                                    <label>申請編號:</label>
                                    <span>#{{ str_pad($selectedApplication->id, 6, '0', STR_PAD_LEFT) }}</span>
                                </div>
                                <div class="detail-item">
                                    <label>姓名:</label>
                                    <span>{{ $selectedApplication->name }}</span>
                                </div>
                                <div class="detail-item">
                                    <label>手機號碼:</label>
                                    <span>{{ $selectedApplication->formatted_phone }}</span>
                                </div>
                                <div class="detail-item">
                                    <label>職業:</label>
                                    <span>{{ $selectedApplication->occupation }}</span>
                                </div>
                                <div class="detail-item">
                                    <label>居住縣市:</label>
                                    <span>{{ $selectedApplication->city }}</span>
                                </div>
                                <div class="detail-item">
                                    <label>方便聯繫時間:</label>
                                    <span>{{ $selectedApplication->contact_time }}</span>
                                </div>
                                @if($selectedApplication->line_id)
                                    <div class="detail-item">
                                        <label>Line ID:</label>
                                        <span>{{ $selectedApplication->line_id }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="detail-section">
                                <h3>申請資料</h3>
                                <div class="detail-item">
                                    <label>申請金額:</label>
                                    <span class="amount-highlight">${{ number_format($selectedApplication->amount, 0) }}</span>
                                </div>
                                <div class="detail-item">
                                    <label>申請狀態:</label>
                                    <span class="status-badge status-{{ $selectedApplication->status }}">
                                        <i class="{{ $selectedApplication->status_icon }}"></i>
                                        {{ $selectedApplication->status_name }}
                                    </span>
                                </div>
                                <div class="detail-item">
                                    <label>申請時間:</label>
                                    <span>{{ $selectedApplication->formatted_applied_at }}</span>
                                </div>
                                @if($selectedApplication->notes)
                                    <div class="detail-item">
                                        <label>備註:</label>
                                        <span>{{ $selectedApplication->notes }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Quick Actions in Modal -->
                        <div class="modal-actions">
                            <button wire:click="updateStatus({{ $selectedApplication->id }}, 'approved')"
                                    class="btn btn-success">
                                <i class="fas fa-check"></i> 核准
                            </button>
                            <button wire:click="updateStatus({{ $selectedApplication->id }}, 'rejected')"
                                    class="btn btn-danger">
                                <i class="fas fa-times"></i> 拒絕
                            </button>
                            <button wire:click="updateStatus({{ $selectedApplication->id }}, 'processing')"
                                    class="btn btn-info">
                                <i class="fas fa-spinner"></i> 處理中
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Loading Overlay -->
    <div wire:loading class="loading-overlay">
        <div class="loading-content">
            <i class="fas fa-spinner fa-spin"></i>
            <span>載入中...</span>
        </div>
    </div>
    <style>
/* 新增的樣式 */
.applicant-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.applicant-name {
    font-weight: 600;
    color: #2c3e50;
}

.applicant-details {
    font-size: 12px;
}

.contact-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.contact-info > div {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
}

.contact-info i {
    width: 12px;
    color: #6c757d;
}

.phone-number {
    font-weight: 500;
}

.line-id {
    color: #00c300;
}

.contact-time {
    color: #6c757d;
}

.date-diff {
    font-size: 11px;
    color: #6c757d;
    display: block;
}

.status-badge i {
    margin-right: 4px;
}

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal-content {
    background: white;
    border-radius: 12px;
    padding: 0;
    max-width: 600px;
    width: 90%;
    max-height: 80vh;
    overflow-y: auto;
}

.modal-header {
    padding: 20px 24px;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-close {
    background: none;
    border: none;
    font-size: 18px;
    cursor: pointer;
    color: #6c757d;
}

.modal-body {
    padding: 24px;
}

.detail-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    margin-bottom: 24px;
}

.detail-section h3 {
    margin-bottom: 16px;
    color: #2c3e50;
    font-size: 16px;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 8px;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #f8f9fa;
}

.detail-item label {
    font-weight: 500;
    color: #495057;
    margin: 0;
}

.detail-item span {
    color: #2c3e50;
}

.amount-highlight {
    font-size: 18px;
    font-weight: 600;
    color: #28a745;
}

.modal-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
    padding-top: 20px;
    border-top: 1px solid #e9ecef;
}

@media (max-width: 768px) {
    .detail-grid {
        grid-template-columns: 1fr;
    }

    .modal-actions {
        flex-direction: column;
    }
}
</style>
</div>

