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
                <div class="dropdown">
                    <button class="btn btn-outline dropdown-toggle">
                        <i class="fas fa-download"></i>
                        匯出 Excel
                    </button>
                    <div class="dropdown-menu">
                        <button wire:click="exportExcel('today')" class="dropdown-item">
                            <i class="fas fa-file-excel"></i> 今日資料
                        </button>
                        <button wire:click="exportExcel('this_week')" class="dropdown-item">
                            <i class="fas fa-file-excel"></i> 本週資料
                        </button>
                        <button wire:click="exportExcel('this_month')" class="dropdown-item">
                            <i class="fas fa-file-excel"></i> 本月資料
                        </button>
                        <button wire:click="exportExcel('all')" class="dropdown-item">
                            <i class="fas fa-file-excel"></i> 全部資料
                        </button>
                    </div>
                </div>
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

    @if (session()->has('error'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Enhanced Statistics Cards -->
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
                <div class="stat-number">{{ number_format($stats['today']) }}</div>
                <div class="stat-label">今日申請</div>
            </div>
        </div>

        <div class="stat-card stat-card--warning">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ number_format($stats['pending']) }}</div>
                <div class="stat-label">待審核</div>
            </div>
        </div>

        <div class="stat-card stat-card--success">
            <div class="stat-icon">
                <i class="fas fa-check"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ number_format($stats['approved']) }}</div>
                <div class="stat-label">已核准</div>
            </div>
        </div>

        <div class="stat-card stat-card--danger">
            <div class="stat-icon">
                <i class="fas fa-times"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ number_format($stats['rejected']) }}</div>
                <div class="stat-label">已拒絕</div>
            </div>
        </div>

        <div class="stat-card stat-card--money">
            <div class="stat-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">${{ number_format($stats['total_amount']) }}</div>
                <div class="stat-label">總申請金額</div>
            </div>
        </div>

        <div class="stat-card stat-card--incomplete">
            <div class="stat-icon">
                <i class="fas fa-tasks"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ number_format($stats['incomplete']) }}</div>
                <div class="stat-label">未完成</div>
            </div>
        </div>

        <div class="stat-card stat-card--complete">
            <div class="stat-icon">
                <i class="fas fa-check-double"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ number_format($stats['complete']) }}</div>
                <div class="stat-label">已完成</div>
            </div>
        </div>
    </div>

    <!-- Enhanced Filters and Search -->
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
                        placeholder="搜尋姓名、手機、職業、城市、地址、緊急聯絡人..."
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

            <!-- Step Filter -->
            <div class="filter-group">
                <label class="filter-label">申請進度</label>
                <select wire:model.live="stepFilter" class="filter-select">
                    <option value="all">全部進度</option>
                    <option value="incomplete">未完成</option>
                    <option value="complete">已完成</option>
                    <option value="1">步驟1</option>
                    <option value="2">步驟2</option>
                    <option value="3">步驟3</option>
                    <option value="4">步驟4</option>
                    <option value="5">步驟5</option>
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

    <!-- Bulk Actions -->
    @if(count($selectedApplications) > 0)
        <div class="bulk-actions-bar">
            <div class="bulk-info">
                已選擇 {{ count($selectedApplications) }} 筆申請
            </div>
            <div class="bulk-buttons">
                <button wire:click="bulkUpdateStatus('approved')" class="btn btn-sm btn-success">
                    <i class="fas fa-check"></i> 批量核准
                </button>
                <button wire:click="bulkUpdateStatus('rejected')" class="btn btn-sm btn-danger">
                    <i class="fas fa-times"></i> 批量拒絕
                </button>
                <button wire:click="bulkUpdateStatus('processing')" class="btn btn-sm btn-info">
                    <i class="fas fa-spinner"></i> 批量處理中
                </button>
                <button
                    wire:click="bulkDelete"
                    onclick="return confirm('確定要批量刪除選中的申請嗎？')"
                    class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-trash"></i> 批量刪除
                </button>
            </div>
        </div>
    @endif

    <!-- Enhanced Table -->
    <div class="table-container">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input
                                type="checkbox"
                                wire:model.live="selectAll"
                                wire:click="toggleSelectAll"
                                class="checkbox-input"
                            >
                        </th>
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
                        <th>申請人基本資料</th>
                        <th>聯絡資訊</th>
                        <th>申請進度</th>
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
                        <th style="width: 150px;">操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $application)
                        <tr class="table-row">
                            <td>
                                <input
                                    type="checkbox"
                                    value="{{ $application->id }}"
                                    wire:model.live="selectedApplications"
                                    class="checkbox-input"
                                >
                            </td>
                            <td class="cell-id">
                                <span class="id-badge">
                                    #{{ str_pad($application->id, 6, '0', STR_PAD_LEFT) }}
                                </span>
                            </td>
                            <td class="cell-applicant">
                                <div class="applicant-info">
                                    <div class="applicant-name">{{ $application->name }}</div>
                                    <div class="applicant-details">
                                        <div><i class="fas fa-briefcase"></i> {{ $application->occupation }}</div>
                                        <div><i class="fas fa-map-marker-alt"></i> {{ $application->city }}</div>
                                        @if($application->address)
                                            <div class="address-preview" title="{{ $application->address }}">
                                                <i class="fas fa-home"></i>
                                                {{ Str::limit($application->address, 20) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="cell-contact">
                                <div class="contact-info">
                                    <div class="phone-number">
                                        <i class="fas fa-phone"></i>
                                        +886{{ $application->phone }}
                                    </div>
                                    @if($application->line_id)
                                        <div class="line-id">
                                            <i class="fab fa-line"></i>
                                            {{ $application->line_id }}
                                        </div>
                                    @endif
                                    <div class="contact-time">
                                        <i class="fas fa-clock"></i>
                                        <small>{{ Str::limit($application->contact_time, 15) }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="cell-progress">
                                <div class="progress-info">
                                    <div class="step-indicator">步驟 {{ $application->current_step }}/5</div>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: {{ ($application->current_step / 5) * 100 }}%"></div>
                                    </div>
                                    <div class="step-badges">
                                        <span class="step-badge {{ $application->step_1_completed ? 'completed' : '' }}">1</span>
                                        <span class="step-badge {{ $application->step_2_completed ? 'completed' : '' }}">2</span>
                                        <span class="step-badge {{ $application->step_3_completed ? 'completed' : '' }}">3</span>
                                        <span class="step-badge {{ $application->step_4_completed ? 'completed' : '' }}">4</span>
                                        <span class="step-badge {{ $application->step_5_completed ? 'completed' : '' }}">5</span>
                                    </div>
                                </div>
                            </td>
                            <td class="cell-amount">
                                <span class="amount-value">${{ number_format($application->amount, 0) }}</span>
                            </td>
                            <td class="cell-status">
                                @php
                                    $statusConfig = [
                                        'pending' => ['class' => 'warning', 'icon' => 'fas fa-clock', 'text' => '待審核'],
                                        'processing' => ['class' => 'info', 'icon' => 'fas fa-spinner', 'text' => '處理中'],
                                        'approved' => ['class' => 'success', 'icon' => 'fas fa-check', 'text' => '已核准'],
                                        'rejected' => ['class' => 'danger', 'icon' => 'fas fa-times', 'text' => '已拒絕']
                                    ];
                                    $config = $statusConfig[$application->status] ?? ['class' => 'secondary', 'icon' => 'fas fa-question', 'text' => $application->status];
                                @endphp
                                <span class="status-badge status-{{ $config['class'] }}">
                                    <i class="{{ $config['icon'] }}"></i>
                                    {{ $config['text'] }}
                                </span>
                            </td>
                            <td class="cell-date">
                                <div class="date-info">
                                    <span class="date-primary">{{ $application->applied_at->format('Y/m/d') }}</span>
                                    <span class="date-secondary">{{ $application->applied_at->format('H:i') }}</span>
                                    <span class="date-diff">{{ $application->applied_at->diffForHumans() }}</span>
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
                                        <button class="btn btn-sm btn-outline dropdown-toggle" title="更新狀態">
                                            <i class="fas fa-edit"></i>
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
                            <td colspan="9" class="empty-state">
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

    <!-- Enhanced Application Detail Modal -->
    @if($showingDetail)
        <div class="modal-overlay" wire:click="closeDetail">
            <div class="modal-content modal-large" wire:click.stop>
                <div class="modal-header">
                    <h2>申請詳細資料 - #{{ str_pad($selectedApplication->id, 6, '0', STR_PAD_LEFT) }}</h2>
                    <div class="modal-header-actions">
                        <button wire:click="toggleDocuments" class="btn btn-sm btn-outline">
                            <i class="fas fa-{{ $showingDocuments ? 'list' : 'images' }}"></i>
                            {{ $showingDocuments ? '基本資料' : '上傳文件' }}
                        </button>
                        <button wire:click="closeDetail" class="modal-close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="modal-body">
                    @if($selectedApplication)
                        @if(!$showingDocuments)
                            <!-- 基本資料 -->
                            <div class="detail-tabs">
                                <div class="tab-content">
                                    <!-- 申請進度 -->
                                    <div class="detail-section progress-section">
                                        <h3><i class="fas fa-tasks"></i> 申請進度</h3>
                                        <div class="application-progress">
                                            <div class="progress-steps">
                                                <div class="progress-step {{ $selectedApplication->step_1_completed ? 'completed' : 'pending' }}">
                                                    <div class="step-icon">
                                                        <i class="fas fa-{{ $selectedApplication->step_1_completed ? 'check' : 'circle' }}"></i>
                                                    </div>
                                                    <div class="step-info">
                                                        <div class="step-title">基本資料</div>
                                                        <div class="step-time">
                                                            {{ $selectedApplication->step_1_completed_at ? $selectedApplication->step_1_completed_at->format('Y/m/d H:i') : '未完成' }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="progress-step {{ $selectedApplication->step_2_completed ? 'completed' : 'pending' }}">
                                                    <div class="step-icon">
                                                        <i class="fas fa-{{ $selectedApplication->step_2_completed ? 'check' : 'circle' }}"></i>
                                                    </div>
                                                    <div class="step-info">
                                                        <div class="step-title">緊急聯絡人</div>
                                                        <div class="step-time">
                                                            {{ $selectedApplication->step_2_completed_at ? $selectedApplication->step_2_completed_at->format('Y/m/d H:i') : '未完成' }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="progress-step {{ $selectedApplication->step_3_completed ? 'completed' : 'pending' }}">
                                                    <div class="step-icon">
                                                        <i class="fas fa-{{ $selectedApplication->step_3_completed ? 'check' : 'circle' }}"></i>
                                                    </div>
                                                    <div class="step-info">
                                                        <div class="step-title">證件上傳</div>
                                                        <div class="step-time">
                                                            {{ $selectedApplication->step_3_completed_at ? $selectedApplication->step_3_completed_at->format('Y/m/d H:i') : '未完成' }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="progress-step {{ $selectedApplication->step_4_completed ? 'completed' : 'pending' }}">
                                                    <div class="step-icon">
                                                        <i class="fas fa-{{ $selectedApplication->step_4_completed ? 'check' : 'circle' }}"></i>
                                                    </div>
                                                    <div class="step-info">
                                                        <div class="step-title">銀行資訊</div>
                                                        <div class="step-time">
                                                            {{ $selectedApplication->step_4_completed_at ? $selectedApplication->step_4_completed_at->format('Y/m/d H:i') : '未完成' }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="progress-step {{ $selectedApplication->step_5_completed ? 'completed' : 'pending' }}">
                                                    <div class="step-icon">
                                                        <i class="fas fa-{{ $selectedApplication->step_5_completed ? 'check' : 'circle' }}"></i>
                                                    </div>
                                                    <div class="step-info">
                                                        <div class="step-title">申請完成</div>
                                                        <div class="step-time">
                                                            {{ $selectedApplication->step_5_completed_at ? $selectedApplication->step_5_completed_at->format('Y/m/d H:i') : '未完成' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="detail-grid">
                                        <!-- 基本資料 -->
                                        <div class="detail-section">
                                            <h3><i class="fas fa-user"></i> 基本資料</h3>
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
                                                <span>+886{{ $selectedApplication->phone }}</span>
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
                                                <label>詳細地址:</label>
                                                <span>{{ $selectedApplication->address ?: '未填寫' }}</span>
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

                                        <!-- 緊急聯絡人資料 -->
                                        <div class="detail-section">
                                            <h3><i class="fas fa-users"></i> 緊急聯絡人</h3>
                                            @if($selectedApplication->emergency_contact_1_name)
                                                <div class="emergency-contact">
                                                    <h4>緊急聯絡人 1</h4>
                                                    <div class="detail-item">
                                                        <label>姓名:</label>
                                                        <span>{{ $selectedApplication->emergency_contact_1_name }}</span>
                                                    </div>
                                                    <div class="detail-item">
                                                        <label>電話:</label>
                                                        <span>+886{{ $selectedApplication->emergency_contact_1_phone }}</span>
                                                    </div>
                                                    <div class="detail-item">
                                                        <label>關係:</label>
                                                        <span>{{ $selectedApplication->emergency_contact_1_relationship }}</span>
                                                    </div>
                                                </div>
                                            @endif

                                            @if($selectedApplication->emergency_contact_2_name)
                                                <div class="emergency-contact">
                                                    <h4>緊急聯絡人 2</h4>
                                                    <div class="detail-item">
                                                        <label>姓名:</label>
                                                        <span>{{ $selectedApplication->emergency_contact_2_name }}</span>
                                                    </div>
                                                    <div class="detail-item">
                                                        <label>電話:</label>
                                                        <span>+886{{ $selectedApplication->emergency_contact_2_phone }}</span>
                                                    </div>
                                                    <div class="detail-item">
                                                        <label>關係:</label>
                                                        <span>{{ $selectedApplication->emergency_contact_2_relationship }}</span>
                                                    </div>
                                                </div>
                                            @endif

                                            @if(!$selectedApplication->emergency_contact_1_name && !$selectedApplication->emergency_contact_2_name)
                                                <p class="no-data">尚未填寫緊急聯絡人資料</p>
                                            @endif
                                        </div>

                                        <!-- 申請資料 -->
                                        <div class="detail-section">
                                            <h3><i class="fas fa-file-contract"></i> 申請資料</h3>
                                            <div class="detail-item">
                                                <label>申請金額:</label>
                                                <span class="amount-highlight">${{ number_format($selectedApplication->amount, 0) }}</span>
                                            </div>
                                            <div class="detail-item">
                                                <label>申請狀態:</label>
                                                <span class="status-badge status-{{ $selectedApplication->status }}">
                                                    <i class="{{ $config['icon'] }}"></i>
                                                    {{ $config['text'] }}
                                                </span>
                                            </div>
                                            <div class="detail-item">
                                                <label>申請時間:</label>
                                                <span>{{ $selectedApplication->applied_at->format('Y年m月d日 H:i:s') }}</span>
                                            </div>
                                            <div class="detail-item">
                                                <label>最後更新:</label>
                                                <span>{{ $selectedApplication->updated_at->format('Y年m月d日 H:i:s') }}</span>
                                            </div>
                                        </div>

                                        <!-- 系統資訊 -->
                                        <div class="detail-section">
                                            <h3><i class="fas fa-cog"></i> 系統資訊</h3>
                                            <div class="detail-item">
                                                <label>當前步驟:</label>
                                                <span>步驟 {{ $selectedApplication->current_step }}/5</span>
                                            </div>
                                            <div class="detail-item">
                                                <label>完成進度:</label>
                                                <span>{{ round(($selectedApplication->current_step / 5) * 100) }}%</span>
                                            </div>
                                            @if($selectedApplication->notes)
                                                <div class="detail-item">
                                                    <label>備註:</label>
                                                    <div class="notes-content">
                                                        {!! nl2br(e($selectedApplication->notes)) !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- 上傳文件 -->
                            <div class="documents-section">
                                <h3><i class="fas fa-folder-open"></i> 上傳文件</h3>
                                <div class="documents-grid">
                                    <!-- 身分證正面 -->
                                    <div class="document-item">
                                        <div class="document-header">
                                            <h4>身分證正面</h4>
                                            @if($selectedApplication->id_card_front_path)
                                                <span class="document-status uploaded">
                                                    <i class="fas fa-check-circle"></i> 已上傳
                                                </span>
                                            @else
                                                <span class="document-status missing">
                                                    <i class="fas fa-times-circle"></i> 未上傳
                                                </span>
                                            @endif
                                        </div>
                                        @if($selectedApplication->id_card_front_path)
                                            @php
                                                $frontUrl = $this->getS3FileUrl($selectedApplication->id_card_front_path);
                                            @endphp
                                            @if($frontUrl)
                                                <div class="document-preview">
                                                    <img src="{{ $frontUrl }}"
                                                        alt="身分證正面"
                                                        class="document-image"
                                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                                    <div style="display:none; text-align:center; padding:20px; color:#666;">
                                                        <i class="fas fa-image"></i><br>
                                                        圖片載入失敗
                                                    </div>
                                                    <div class="document-actions">
                                                        <a href="{{ $frontUrl }}" target="_blank" class="btn btn-sm btn-info">
                                                            <i class="fas fa-external-link-alt"></i> 查看
                                                        </a>
                                                    </div>
                                                </div>
                                            @else
                                                <div style="text-align:center; padding:20px; color:#666;">
                                                    <i class="fas fa-exclamation-triangle"></i><br>
                                                    無法載入圖片
                                                </div>
                                            @endif
                                        @endif
                                    </div>

                                    <!-- 身分證反面 -->
                                    <div class="document-item">
                                        <div class="document-header">
                                            <h4>身分證反面</h4>
                                            @if($selectedApplication->id_card_back_path)
                                                <span class="document-status uploaded">
                                                    <i class="fas fa-check-circle"></i> 已上傳
                                                </span>
                                            @else
                                                <span class="document-status missing">
                                                    <i class="fas fa-times-circle"></i> 未上傳
                                                </span>
                                            @endif
                                        </div>
                                        @if($selectedApplication->id_card_back_path)
                                            @php
                                                $backUrl = $this->getS3FileUrl($selectedApplication->id_card_back_path);
                                            @endphp
                                            @if($backUrl)
                                                <div class="document-preview">
                                                    <img src="{{ $backUrl }}"
                                                        alt="身分證反面"
                                                        class="document-image"
                                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                                    <div style="display:none; text-align:center; padding:20px; color:#666;">
                                                        <i class="fas fa-image"></i><br>
                                                        圖片載入失敗
                                                    </div>
                                                    <div class="document-actions">
                                                        <a href="{{ $backUrl }}" target="_blank" class="btn btn-sm btn-info">
                                                            <i class="fas fa-external-link-alt"></i> 查看
                                                        </a>
                                                    </div>
                                                </div>
                                            @else
                                                <div style="text-align:center; padding:20px; color:#666;">
                                                    <i class="fas fa-exclamation-triangle"></i><br>
                                                    無法載入圖片
                                                </div>
                                            @endif
                                        @endif
                                    </div>

                                    <!-- 手持身分證自拍 -->
                                    <div class="document-item">
                                        <div class="document-header">
                                            <h4>手持身分證自拍</h4>
                                            @if($selectedApplication->id_card_selfie_path)
                                                <span class="document-status uploaded">
                                                    <i class="fas fa-check-circle"></i> 已上傳
                                                </span>
                                            @else
                                                <span class="document-status missing">
                                                    <i class="fas fa-times-circle"></i> 未上傳
                                                </span>
                                            @endif
                                        </div>
                                        @if($selectedApplication->id_card_selfie_path)
                                            @php
                                                $selfieUrl = $this->getS3FileUrl($selectedApplication->id_card_selfie_path);
                                            @endphp
                                            @if($selfieUrl)
                                                <div class="document-preview">
                                                    <img src="{{ $selfieUrl }}"
                                                        alt="手持身分證自拍"
                                                        class="document-image"
                                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                                    <div style="display:none; text-align:center; padding:20px; color:#666;">
                                                        <i class="fas fa-image"></i><br>
                                                        圖片載入失敗
                                                    </div>
                                                    <div class="document-actions">
                                                        <a href="{{ $selfieUrl }}" target="_blank" class="btn btn-sm btn-info">
                                                            <i class="fas fa-external-link-alt"></i> 查看
                                                        </a>
                                                    </div>
                                                </div>
                                            @else
                                                <div style="text-align:center; padding:20px; color:#666;">
                                                    <i class="fas fa-exclamation-triangle"></i><br>
                                                    無法載入圖片
                                                </div>
                                            @endif
                                        @endif
                                    </div>

                                    <!-- 第二證件 -->
                                    <div class="document-item">
                                        <div class="document-header">
                                            <h4>第二證件</h4>
                                            @if($selectedApplication->second_document_path)
                                                <span class="document-status uploaded">
                                                    <i class="fas fa-check-circle"></i> 已上傳
                                                </span>
                                            @else
                                                <span class="document-status missing">
                                                    <i class="fas fa-times-circle"></i> 未上傳
                                                </span>
                                            @endif
                                        </div>
                                        @if($selectedApplication->second_document_path)
                                            @php
                                                $secondDocUrl = $this->getS3FileUrl($selectedApplication->second_document_path);
                                            @endphp
                                            @if($secondDocUrl)
                                                <div class="document-preview">
                                                    <img src="{{ $secondDocUrl }}"
                                                        alt="第二證件"
                                                        class="document-image"
                                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                                    <div style="display:none; text-align:center; padding:20px; color:#666;">
                                                        <i class="fas fa-image"></i><br>
                                                        圖片載入失敗
                                                    </div>
                                                    <div class="document-actions">
                                                        <a href="{{ $secondDocUrl }}" target="_blank" class="btn btn-sm btn-info">
                                                            <i class="fas fa-external-link-alt"></i> 查看
                                                        </a>
                                                    </div>
                                                </div>
                                            @else
                                                <div style="text-align:center; padding:20px; color:#666;">
                                                    <i class="fas fa-exclamation-triangle"></i><br>
                                                    無法載入圖片
                                                </div>
                                            @endif
                                        @endif
                                    </div>

                                    <!-- 銀行卡/存摺 -->
                                    <div class="document-item">
                                        <div class="document-header">
                                            <h4>銀行卡/存摺 <span class="optional">(選填)</span></h4>
                                            @if($selectedApplication->bank_card_path)
                                                <span class="document-status uploaded">
                                                    <i class="fas fa-check-circle"></i> 已上傳
                                                </span>
                                            @else
                                                <span class="document-status optional">
                                                    <i class="fas fa-minus-circle"></i> 未上傳
                                                </span>
                                            @endif
                                        </div>
                                        @if($selectedApplication->bank_card_path)
                                            @php
                                                $bankCardUrl = $this->getS3FileUrl($selectedApplication->bank_card_path);
                                            @endphp
                                            @if($bankCardUrl)
                                                <div class="document-preview">
                                                    <img src="{{ $bankCardUrl }}"
                                                        alt="銀行卡/存摺"
                                                        class="document-image"
                                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                                    <div style="display:none; text-align:center; padding:20px; color:#666;">
                                                        <i class="fas fa-image"></i><br>
                                                        圖片載入失敗
                                                    </div>
                                                    <div class="document-actions">
                                                        <a href="{{ $bankCardUrl }}" target="_blank" class="btn btn-sm btn-info">
                                                            <i class="fas fa-external-link-alt"></i> 查看
                                                        </a>
                                                    </div>
                                                </div>
                                            @else
                                                <div style="text-align:center; padding:20px; color:#666;">
                                                    <i class="fas fa-exclamation-triangle"></i><br>
                                                    無法載入圖片
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

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
                            <button wire:click="updateStatus({{ $selectedApplication->id }}, 'pending')"
                                    class="btn btn-warning">
                                <i class="fas fa-clock"></i> 待審核
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Loading Overlay -->
    <div wire:loading.flex class="loading-overlay">
        <div class="loading-content">
            <i class="fas fa-spinner fa-spin"></i>
            <span>載入中...</span>
        </div>
    </div>

<style>

</style>
</div>
