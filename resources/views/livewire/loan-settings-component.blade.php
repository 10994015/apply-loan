<div class="admin-container">
    <!-- Header -->
    <div class="admin-header">
        <div class="header-content">
            <div class="header-left">
                <h1 class="page-title">
                    <i class="fas fa-cog"></i>
                    貸款設定管理
                </h1>
                <p class="page-subtitle">管理系統的貸款相關參數設定</p>
            </div>
            <div class="header-actions">
                @if(false)
                <button wire:click="showAddForm" class="btn btn-success">
                    <i class="fas fa-plus"></i>
                    新增設定
                </button>
                @endif
                <button wire:click="exportSettings" class="btn btn-outline">
                    <i class="fas fa-download"></i>
                    匯出設定
                </button>
                <button wire:click="resetToDefaults"
                        onclick="return confirm('確定要重置為預設設定嗎？這將清除所有自定義設定！')"
                        class="btn btn-danger">
                    <i class="fas fa-undo"></i>
                    重置預設
                </button>
                <button wire:click="loadSettings" class="btn btn-primary">
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

    <!-- Unsaved Changes Warning -->
    @if($hasChanges)
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            您有未保存的變更，請記得點擊「保存」按鈕來儲存修改。
        </div>
    @endif

    <!-- Settings Cards by Category -->
    <div class="settings-sections">
        @foreach($groupedSettings as $category => $categorySettings)
            <div class="settings-section">
                <div class="section-header">
                    <h2 class="section-title">
                        @switch($category)
                            @case('金額設定')
                                <i class="fas fa-dollar-sign"></i>
                                @break
                            @case('期限設定')
                                <i class="fas fa-calendar-alt"></i>
                                @break
                            @case('利率設定')
                                <i class="fas fa-percentage"></i>
                                @break
                            @default
                                <i class="fas fa-cog"></i>
                        @endswitch
                        {{ $category }}
                    </h2>
                </div>

                <div class="settings-grid">
                    @foreach($categorySettings as $setting)
                        <div class="setting-card {{ !$setting['is_active'] ? 'disabled' : '' }}">
                            <div class="setting-header">
                                <div class="setting-info">
                                    <h3 class="setting-name">{{ $setting['display_name'] }}</h3>
                                    <p class="setting-key">{{ $setting['setting_key'] }}</p>
                                </div>
                                <div class="setting-actions">
                                    <div class="toggle-switch">
                                        <input type="checkbox"
                                               id="active_{{ $setting['id'] }}"
                                               wire:click="toggleActive({{ $setting['id'] }})"
                                               {{ $setting['is_active'] ? 'checked' : '' }}>
                                        <label for="active_{{ $setting['id'] }}"></label>
                                    </div>
                                </div>
                            </div>

                            <div class="setting-body">
                                @if(isset($editingSettings[$setting['id']]))
                                    <!-- Edit Mode -->
                                    <div class="edit-form">
                                        <div class="form-group">
                                            <label class="form-label">設定值</label>
                                            <input type="text"
                                                   wire:model.defer="settings.{{ $setting['id'] }}.setting_value"
                                                   class="form-input"
                                                   placeholder="請輸入設定值">
                                            @error('settings.' . $setting['id'] . '.setting_value')
                                                <span class="error-message">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label">說明</label>
                                            <input type="text"
                                                   wire:model.defer="settings.{{ $setting['id'] }}.description"
                                                   class="form-input"
                                                   placeholder="請輸入設定說明">
                                        </div>

                                        <div class="form-actions">
                                            <button wire:click="saveSetting({{ $setting['id'] }})"
                                                    class="btn btn-sm btn-success">
                                                <i class="fas fa-check"></i> 保存
                                            </button>
                                            <button wire:click="cancelEdit({{ $setting['id'] }})"
                                                    class="btn btn-sm btn-secondary">
                                                <i class="fas fa-times"></i> 取消
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <!-- View Mode -->
                                    <div class="setting-value">
                                        <div class="value-display">
                                            <span class="value-number">
                                                @if($setting['setting_type'] === 'decimal')
                                                    {{ number_format((float)$setting['setting_value'], 2) }}
                                                @elseif($setting['setting_type'] === 'integer')
                                                    {{ number_format((int)$setting['setting_value']) }}
                                                @elseif($setting['setting_type'] === 'boolean')
                                                    {{ $setting['setting_value'] ? '是' : '否' }}
                                                @else
                                                    {{ $setting['setting_value'] }}
                                                @endif
                                            </span>
                                            <span class="value-type">
                                                @if($setting['setting_type'] === 'decimal' && str_contains($setting['setting_key'], 'rate'))
                                                    %
                                                @elseif($setting['setting_type'] === 'integer' && str_contains($setting['setting_key'], 'amount'))
                                                    元
                                                @elseif($setting['setting_type'] === 'integer' && str_contains($setting['setting_key'], 'days'))
                                                    天
                                                @endif
                                            </span>
                                        </div>
                                        <div class="value-type-badge">{{ $setting['setting_type'] }}</div>
                                    </div>

                                    <div class="setting-description">
                                        {{ $setting['description'] }}
                                    </div>

                                    <div class="setting-controls">
                                        <button wire:click="toggleEdit({{ $setting['id'] }})"
                                                class="btn btn-sm btn-outline">
                                            <i class="fas fa-edit"></i> 編輯
                                        </button>
                                        @if(false)
                                        <button wire:click="deleteSetting({{ $setting['id'] }})"
                                                onclick="return confirm('確定要刪除此設定嗎？')"
                                                class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i> 刪除
                                        </button>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        @if(empty($groupedSettings))
            <div class="empty-state">
                <div class="empty-content">
                    <i class="fas fa-cog"></i>
                    <h3>尚無設定項目</h3>
                    <p>點擊「新增設定」來建立第一個設定項目</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Add Setting Modal -->
    @if($showAddModal)
        <div class="modal-overlay" wire:click="hideAddForm">
            <div class="modal-content" wire:click.stop>
                <div class="modal-header">
                    <h2>新增設定項目</h2>
                    <button wire:click="hideAddForm" class="modal-close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="addSetting">
                        <div class="form-group">
                            <label class="form-label">設定鍵名 <span class="required">*</span></label>
                            <input type="text"
                                   wire:model.defer="newSettingKey"
                                   class="form-input"
                                   placeholder="例如：loan_custom_setting">
                            @error('newSettingKey')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">設定類型 <span class="required">*</span></label>
                            <select wire:model="newSettingType" class="form-select">
                                <option value="string">字串 (string)</option>
                                <option value="integer">整數 (integer)</option>
                                <option value="decimal">小數 (decimal)</option>
                                <option value="boolean">布林值 (boolean)</option>
                            </select>
                            @error('newSettingType')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">設定值 <span class="required">*</span></label>
                            @if($newSettingType === 'boolean')
                                <select wire:model.defer="newSettingValue" class="form-select">
                                    <option value="">請選擇...</option>
                                    <option value="1">是 (true)</option>
                                    <option value="0">否 (false)</option>
                                </select>
                            @else
                                <input type="{{ $newSettingType === 'decimal' ? 'number' : 'text' }}"
                                       wire:model.defer="newSettingValue"
                                       class="form-input"
                                       {{ $newSettingType === 'decimal' ? 'step=0.01' : '' }}
                                       placeholder="請輸入設定值">
                            @endif
                            @error('newSettingValue')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">說明</label>
                            <input type="text"
                                   wire:model.defer="newSettingDescription"
                                   class="form-input"
                                   placeholder="請輸入設定說明">
                            @error('newSettingDescription')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-plus"></i> 新增設定
                            </button>
                            <button type="button" wire:click="hideAddForm" class="btn btn-secondary">
                                <i class="fas fa-times"></i> 取消
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Loading Overlay -->
    <div wire:loading.flex class="loading-overlay">
        <div class="loading-content">
            <i class="fas fa-spinner fa-spin"></i>
            <span>處理中...</span>
        </div>
    </div>
</div>
@push('styles')

<style>
.admin-container {
    padding: 24px;
    max-width: 1400px;
    margin: 0 auto;
}

/* Header */
.admin-header {
    background: white;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header-left h1 {
    font-size: 28px;
    font-weight: 700;
    color: #1f2937;
    margin: 0 0 4px 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.header-left p {
    color: #6b7280;
    margin: 0;
}

.header-actions {
    display: flex;
    gap: 12px;
}

/* Alerts */
.alert {
    padding: 16px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.alert-success {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #166534;
}

.alert-danger {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #dc2626;
}

.alert-warning {
    background: #fffbeb;
    border: 1px solid #fed7aa;
    color: #d97706;
}

/* Settings Sections */
.settings-sections {
    display: flex;
    flex-direction: column;
    gap: 32px;
}

.settings-section {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.section-header {
    background: #f8fafc;
    border-bottom: 1px solid #e5e7eb;
    padding: 20px 24px;
}

.section-title {
    font-size: 20px;
    font-weight: 600;
    color: #374151;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.settings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 24px;
    padding: 24px;
}

/* Setting Cards */
.setting-card {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 20px;
    transition: all 0.2s;
}

.setting-card:hover {
    border-color: #3b82f6;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
}

.setting-card.disabled {
    opacity: 0.6;
    background: #f9fafb;
}

.setting-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 16px;
}

.setting-info h3 {
    font-size: 16px;
    font-weight: 600;
    color: #374151;
    margin: 0 0 4px 0;
}

.setting-key {
    font-size: 12px;
    color: #6b7280;
    font-family: 'Courier New', monospace;
    background: #f3f4f6;
    padding: 2px 6px;
    border-radius: 4px;
    margin: 0;
}

/* Toggle Switch */
.toggle-switch {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 24px;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-switch label {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #d1d5db;
    transition: .4s;
    border-radius: 24px;
}

.toggle-switch label:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

.toggle-switch input:checked + label {
    background-color: #3b82f6;
}

.toggle-switch input:checked + label:before {
    transform: translateX(20px);
}

/* Setting Body */
.setting-value {
    margin-bottom: 12px;
}

.value-display {
    display: flex;
    align-items: baseline;
    gap: 4px;
    margin-bottom: 8px;
}

.value-number {
    font-size: 24px;
    font-weight: 700;
    color: #1f2937;
}

.value-type {
    font-size: 16px;
    color: #6b7280;
}

.value-type-badge {
    display: inline-block;
    background: #e5e7eb;
    color: #374151;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
    text-transform: uppercase;
}

.setting-description {
    color: #6b7280;
    font-size: 14px;
    margin-bottom: 16px;
    line-height: 1.5;
}

.setting-controls {
    display: flex;
    gap: 8px;
}

/* Edit Form */
.edit-form .form-group {
    margin-bottom: 16px;
}

.form-label {
    display: block;
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    margin-bottom: 6px;
}

.form-input, .form-select {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 14px;
    transition: border-color 0.2s;
}

.form-input:focus, .form-select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-actions {
    display: flex;
    gap: 8px;
    margin-top: 16px;
}

.error-message {
    display: block;
    color: #dc2626;
    font-size: 12px;
    margin-top: 4px;
}

.required {
    color: #dc2626;
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-primary {
    background: #3b82f6;
    color: white;
}

.btn-primary:hover {
    background: #2563eb;
}

.btn-success {
    background: #10b981;
    color: white;
}

.btn-success:hover {
    background: #059669;
}

.btn-danger {
    background: #ef4444;
    color: white;
}

.btn-danger:hover {
    background: #dc2626;
}

.btn-outline {
    background: white;
    border: 1px solid #d1d5db;
    color: #374151;
}

.btn-outline:hover {
    background: #f9fafb;
}

.btn-outline-danger {
    background: white;
    border: 1px solid #ef4444;
    color: #ef4444;
}

.btn-outline-danger:hover {
    background: #fef2f2;
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 13px;
}

/* Modal */
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
    width: 90%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 1px solid #e5e7eb;
}

.modal-header h2 {
    font-size: 20px;
    font-weight: 600;
    color: #374151;
    margin: 0;
}

.modal-close {
    background: none;
    border: none;
    font-size: 18px;
    color: #6b7280;
    cursor: pointer;
    padding: 4px;
}

.modal-close:hover {
    color: #374151;
}

.modal-body {
    padding: 24px;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #6b7280;
}

.empty-content i {
    font-size: 48px;
    margin-bottom: 16px;
    color: #d1d5db;
}

.empty-content h3 {
    font-size: 18px;
    font-weight: 600;
    color: #374151;
    margin: 0 0 8px 0;
}

.empty-content p {
    margin: 0;
}

/* Loading Overlay */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.loading-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    font-size: 16px;
    color: #6b7280;
}

.loading-content i {
    font-size: 24px;
    color: #3b82f6;
}

/* Responsive */
@media (max-width: 768px) {
    .admin-container {
        padding: 16px;
    }

    .header-content {
        flex-direction: column;
        gap: 16px;
        align-items: stretch;
    }

    .header-actions {
        flex-wrap: wrap;
    }

    .settings-grid {
        grid-template-columns: 1fr;
        padding: 16px;
    }

    .setting-header {
        flex-direction: column;
        gap: 12px;
    }

    .modal-content {
        margin: 20px;
        width: calc(100% - 40px);
    }
}
</style>

@endpush
