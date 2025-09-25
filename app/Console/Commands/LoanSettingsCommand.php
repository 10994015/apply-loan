<?php

namespace App\Console\Commands;

use App\Models\LoanSetting;
use App\Helpers\LoanSettingsHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LoanSettingsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loan:settings
                           {action : Action to perform (list|get|set|reset|cache|clear-cache)}
                           {key? : Setting key for get/set actions}
                           {value? : Setting value for set action}
                           {--type=string : Setting type for set action (string|integer|decimal|boolean)}
                           {--description= : Setting description for set action}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage loan settings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'list':
                return $this->listSettings();
            case 'get':
                return $this->getSetting();
            case 'set':
                return $this->setSetting();
            case 'reset':
                return $this->resetSettings();
            case 'cache':
                return $this->warmCache();
            case 'clear-cache':
                return $this->clearCache();
            default:
                $this->error("Unknown action: {$action}");
                return 1;
        }
    }

    /**
     * 列出所有設定
     */
    protected function listSettings()
    {
        $settings = LoanSetting::orderBy('setting_key')->get();

        if ($settings->isEmpty()) {
            $this->info('No settings found.');
            return 0;
        }

        $headers = ['ID', 'Key', 'Value', 'Type', 'Active', 'Description'];
        $rows = [];

        foreach ($settings as $setting) {
            $rows[] = [
                $setting->id,
                $setting->setting_key,
                $this->formatValue($setting->setting_value, $setting->setting_type),
                $setting->setting_type,
                $setting->is_active ? 'Yes' : 'No',
                $setting->description ?? 'N/A'
            ];
        }

        $this->table($headers, $rows);

        $this->info("\nTotal settings: " . $settings->count());
        $this->info("Active settings: " . $settings->where('is_active', true)->count());

        return 0;
    }

    /**
     * 獲取特定設定
     */
    protected function getSetting()
    {
        $key = $this->argument('key');

        if (!$key) {
            $this->error('Setting key is required for get action.');
            return 1;
        }

        $value = LoanSettingsHelper::get($key);

        if ($value === null) {
            $this->error("Setting '{$key}' not found or is inactive.");
            return 1;
        }

        $this->info("Setting '{$key}': " . $this->formatDisplayValue($value));
        return 0;
    }

    /**
     * 設定值
     */
    protected function setSetting()
    {
        $key = $this->argument('key');
        $value = $this->argument('value');
        $type = $this->option('type') ?? 'string';
        $description = $this->option('description');

        if (!$key) {
            $this->error('Setting key is required for set action.');
            return 1;
        }

        if ($value === null) {
            $this->error('Setting value is required for set action.');
            return 1;
        }

        // 驗證類型
        if (!in_array($type, ['string', 'integer', 'decimal', 'boolean'])) {
            $this->error('Invalid setting type. Must be one of: string, integer, decimal, boolean');
            return 1;
        }

        // 驗證值的格式
        if (!$this->validateValueByType($value, $type)) {
            $this->error("Value '{$value}' is not valid for type '{$type}'.");
            return 1;
        }

        try {
            $setting = LoanSettingsHelper::set($key, $value, $type, $description);

            if ($setting) {
                $this->info("Setting '{$key}' has been set to: " . $this->formatValue($value, $type));
                return 0;
            } else {
                $this->error("Failed to set setting '{$key}'.");
                return 1;
            }
        } catch (\Exception $e) {
            $this->error("Error setting '{$key}': " . $e->getMessage());
            return 1;
        }
    }

    /**
     * 重置設定為預設值
     */
    protected function resetSettings()
    {
        if (!$this->confirm('Are you sure you want to reset all loan settings to defaults? This will delete all custom settings.')) {
            $this->info('Reset cancelled.');
            return 0;
        }

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

            // 清除快取
            LoanSettingsHelper::clearCache();

            $this->info('Loan settings have been reset to defaults successfully.');
            return 0;

        } catch (\Exception $e) {
            $this->error('Error resetting settings: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * 預熱快取
     */
    protected function warmCache()
    {
        $this->info('Warming loan settings cache...');

        if (LoanSettingsHelper::warmCache()) {
            $this->info('Cache warmed successfully.');
            return 0;
        } else {
            $this->error('Failed to warm cache.');
            return 1;
        }
    }

    /**
     * 清除快取
     */
    protected function clearCache()
    {
        $this->info('Clearing loan settings cache...');

        LoanSettingsHelper::clearCache();

        $this->info('Cache cleared successfully.');
        return 0;
    }

    /**
     * 格式化顯示值
     */
    protected function formatValue($value, $type)
    {
        switch ($type) {
            case 'decimal':
                return number_format((float)$value, 2);
            case 'integer':
                return number_format((int)$value);
            case 'boolean':
                return $value ? 'true' : 'false';
            default:
                return (string)$value;
        }
    }

    /**
     * 格式化顯示值（給用戶看）
     */
    protected function formatDisplayValue($value)
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if (is_numeric($value)) {
            return number_format($value, is_float($value) ? 2 : 0);
        }
        return (string)$value;
    }

    /**
     * 根據類型驗證值
     */
    protected function validateValueByType($value, $type)
    {
        switch ($type) {
            case 'integer':
                return is_numeric($value) && (string)(int)$value === (string)$value;
            case 'decimal':
                return is_numeric($value);
            case 'boolean':
                return in_array(strtolower($value), ['true', 'false', '1', '0', 'yes', 'no']);
            default:
                return true;
        }
    }
}
