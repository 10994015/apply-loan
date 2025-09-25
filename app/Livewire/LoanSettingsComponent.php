<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LoanSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class LoanSettingsComponent extends Component
{
    // 設定值
    public $settings = [];
    public $originalSettings = [];

    // 編輯模式
    public $editingSettings = [];

    // 新增設定
    public $showAddModal = false;
    public $newSettingKey = '';
    public $newSettingValue = '';
    public $newSettingType = 'string';
    public $newSettingDescription = '';

    // 驗證規則
    protected $rules = [
        'settings.*.setting_value' => 'required',
        'newSettingKey' => 'required|string|max:100|unique:loan_settings,setting_key',
        'newSettingValue' => 'required',
        'newSettingType' => 'required|in:string,integer,decimal,boolean',
        'newSettingDescription' => 'nullable|string|max:255'
    ];

    protected $messages = [
        'settings.*.setting_value.required' => '設定值不能為空',
        'newSettingKey.required' => '設定鍵名不能為空',
        'newSettingKey.unique' => '設定鍵名已存在',
        'newSettingValue.required' => '設定值不能為空',
        'newSettingType.required' => '設定類型不能為空',
    ];

    public function mount()
    {
        $this->loadSettings();
    }

    public function loadSettings()
    {
        $dbSettings = LoanSetting::orderBy('setting_key')->get();

        $this->settings = [];
        $this->originalSettings = [];

        foreach ($dbSettings as $setting) {
            $this->settings[$setting->id] = [
                'id' => $setting->id,
                'setting_key' => $setting->setting_key,
                'setting_value' => $setting->setting_value,
                'setting_type' => $setting->setting_type,
                'description' => $setting->description,
                'is_active' => $setting->is_active,
                'display_name' => $setting->display_name,
                'category' => $setting->category,
            ];

            $this->originalSettings[$setting->id] = $this->settings[$setting->id];
        }
    }

    public function toggleEdit($settingId)
    {
        if (isset($this->editingSettings[$settingId])) {
            unset($this->editingSettings[$settingId]);
        } else {
            $this->editingSettings[$settingId] = true;
        }
    }

    public function saveSetting($settingId)
    {
        // 驗證單個設定
        $this->validateOnly("settings.{$settingId}.setting_value");

        try {
            $setting = LoanSetting::find($settingId);
            if ($setting) {
                $newValue = $this->settings[$settingId]['setting_value'];

                // 根據類型驗證值
                if (!$this->validateValueByType($newValue, $setting->setting_type)) {
                    session()->flash('error', "設定值格式不正確（{$setting->display_name}）");
                    return;
                }

                $setting->update([
                    'setting_value' => $newValue,
                    'description' => $this->settings[$settingId]['description'],
                    'is_active' => $this->settings[$settingId]['is_active'],
                ]);

                // 更新原始設定以防止誤判為已修改
                $this->originalSettings[$settingId] = $this->settings[$settingId];

                unset($this->editingSettings[$settingId]);

                session()->flash('message', "設定已更新：{$setting->display_name}");

                Log::info('Loan setting updated', [
                    'setting_key' => $setting->setting_key,
                    'old_value' => $setting->getOriginal('setting_value'),
                    'new_value' => $newValue,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to update loan setting', [
                'setting_id' => $settingId,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', '更新設定時發生錯誤');
        }
    }

    public function cancelEdit($settingId)
    {
        // 恢復原始值
        if (isset($this->originalSettings[$settingId])) {
            $this->settings[$settingId] = $this->originalSettings[$settingId];
        }
        unset($this->editingSettings[$settingId]);
    }

    public function toggleActive($settingId)
    {
        try {
            $setting = LoanSetting::find($settingId);
            if ($setting) {
                $setting->update(['is_active' => !$setting->is_active]);
                $this->loadSettings();

                $status = $setting->is_active ? '啟用' : '停用';
                session()->flash('message', "已{$status}設定：{$setting->display_name}");
            }
        } catch (\Exception $e) {
            Log::error('Failed to toggle setting status', [
                'setting_id' => $settingId,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', '切換設定狀態時發生錯誤');
        }
    }

    public function deleteSetting($settingId)
    {
        try {
            $setting = LoanSetting::find($settingId);
            if ($setting) {
                $displayName = $setting->display_name;
                $setting->delete();
                $this->loadSettings();

                session()->flash('message', "已刪除設定：{$displayName}");

                Log::info('Loan setting deleted', [
                    'setting_key' => $setting->setting_key,
                    'setting_value' => $setting->setting_value,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to delete loan setting', [
                'setting_id' => $settingId,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', '刪除設定時發生錯誤');
        }
    }

    public function showAddForm()
    {
        $this->showAddModal = true;
        $this->resetAddForm();
    }

    public function hideAddForm()
    {
        $this->showAddModal = false;
        $this->resetAddForm();
    }

    public function addSetting()
    {
        $this->validate([
            'newSettingKey' => 'required|string|max:100|unique:loan_settings,setting_key',
            'newSettingValue' => 'required',
            'newSettingType' => 'required|in:string,integer,decimal,boolean',
            'newSettingDescription' => 'nullable|string|max:255'
        ]);

        try {
            // 驗證值的格式
            if (!$this->validateValueByType($this->newSettingValue, $this->newSettingType)) {
                session()->flash('error', '設定值格式不符合所選類型');
                return;
            }

            LoanSetting::create([
                'setting_key' => $this->newSettingKey,
                'setting_value' => $this->newSettingValue,
                'setting_type' => $this->newSettingType,
                'description' => $this->newSettingDescription ?: "Setting for {$this->newSettingKey}",
                'is_active' => true,
            ]);

            $this->loadSettings();
            $this->hideAddForm();

            session()->flash('message', "已新增設定：{$this->newSettingKey}");

        } catch (\Exception $e) {
            Log::error('Failed to add loan setting', [
                'setting_key' => $this->newSettingKey,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', '新增設定時發生錯誤');
        }
    }

    public function resetToDefaults()
    {
        try {
            DB::transaction(function () {
                // 清除現有設定
                LoanSetting::truncate();

                // 重新插入預設設定
                $defaultSettings = [
                    [
                        'setting_key' => 'loan_min_amount',
                        'setting_value' => '7000',
                        'setting_type' => 'integer',
                        'description' => '貸款最小金額',
                        'is_active' => true,
                    ],
                    [
                        'setting_key' => 'loan_max_amount',
                        'setting_value' => '100000',
                        'setting_type' => 'integer',
                        'description' => '貸款最大金額',
                        'is_active' => true,
                    ],
                    [
                        'setting_key' => 'loan_default_amount',
                        'setting_value' => '20000',
                        'setting_type' => 'integer',
                        'description' => '貸款預設金額',
                        'is_active' => true,
                    ],
                    [
                        'setting_key' => 'loan_min_days',
                        'setting_value' => '91',
                        'setting_type' => 'integer',
                        'description' => '貸款最少天數',
                        'is_active' => true,
                    ],
                    [
                        'setting_key' => 'loan_max_days',
                        'setting_value' => '365',
                        'setting_type' => 'integer',
                        'description' => '貸款最多天數',
                        'is_active' => true,
                    ],
                    [
                        'setting_key' => 'loan_daily_rate',
                        'setting_value' => '0.03',
                        'setting_type' => 'decimal',
                        'description' => '最低日利率(%)',
                        'is_active' => true,
                    ],
                ];

                foreach ($defaultSettings as $setting) {
                    LoanSetting::create(array_merge($setting, [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]));
                }
            });

            $this->loadSettings();
            session()->flash('message', '已重置為預設設定');

            Log::info('Loan settings reset to defaults');

        } catch (\Exception $e) {
            Log::error('Failed to reset loan settings', [
                'error' => $e->getMessage()
            ]);
            session()->flash('error', '重置設定時發生錯誤');
        }
    }

    public function exportSettings()
    {
        try {
            $settings = LoanSetting::orderBy('setting_key')->get();
            $exportData = $settings->toArray();

            $filename = 'loan_settings_export_' . now()->format('Y_m_d_H_i_s') . '.json';

            return response()->streamDownload(function () use ($exportData) {
                echo json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }, $filename, [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to export loan settings', [
                'error' => $e->getMessage()
            ]);
            session()->flash('error', '匯出設定時發生錯誤');
        }
    }

    private function validateValueByType($value, $type)
    {
        switch ($type) {
            case 'integer':
                return is_numeric($value) && (string)(int)$value === (string)$value;
            case 'decimal':
                return is_numeric($value);
            case 'boolean':
                return in_array(strtolower($value), ['true', 'false', '1', '0', 'yes', 'no', '是', '否']);
            default:
                return true;
        }
    }

    private function resetAddForm()
    {
        $this->newSettingKey = '';
        $this->newSettingValue = '';
        $this->newSettingType = 'string';
        $this->newSettingDescription = '';
    }

    public function getGroupedSettingsProperty()
    {
        $grouped = [];
        foreach ($this->settings as $setting) {
            $category = $setting['category'];
            if (!isset($grouped[$category])) {
                $grouped[$category] = [];
            }
            $grouped[$category][] = $setting;
        }
        return $grouped;
    }

    public function hasUnsavedChanges()
    {
        foreach ($this->settings as $id => $setting) {
            if (isset($this->originalSettings[$id])) {
                if ($setting['setting_value'] !== $this->originalSettings[$id]['setting_value'] ||
                    $setting['description'] !== $this->originalSettings[$id]['description'] ||
                    $setting['is_active'] !== $this->originalSettings[$id]['is_active']) {
                    return true;
                }
            }
        }
        return false;
    }

    public function render()
    {
        return view('livewire.loan-settings-component', [
            'groupedSettings' => $this->getGroupedSettingsProperty(),
            'hasChanges' => $this->hasUnsavedChanges(),
        ])->layout('layouts.admin', ['title' => '貸款設定管理']);
    }
}
