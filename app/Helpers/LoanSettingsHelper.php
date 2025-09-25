<?php

namespace App\Helpers;

use App\Models\LoanSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class LoanSettingsHelper
{
    // 快取時間（分鐘）
    const CACHE_DURATION = 60;

    // 快取鍵前綴
    const CACHE_PREFIX = 'loan_setting_';

    /**
     * 獲取設定值（帶快取）
     */
    public static function get($key, $default = null)
    {
        $cacheKey = self::CACHE_PREFIX . $key;

        try {
            return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($key, $default) {
                $setting = LoanSetting::where('setting_key', $key)
                                    ->where('is_active', true)
                                    ->first();

                if (!$setting) {
                    return $default;
                }

                return self::castValue($setting->setting_value, $setting->setting_type);
            });
        } catch (\Exception $e) {
            Log::error('Failed to get loan setting', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return $default;
        }
    }

    /**
     * 設定值（自動清除快取）
     */
    public static function set($key, $value, $type = 'string', $description = null)
    {
        try {
            $setting = LoanSetting::updateOrCreate(
                ['setting_key' => $key],
                [
                    'setting_value' => $value,
                    'setting_type' => $type,
                    'description' => $description ?? "Setting for {$key}",
                    'is_active' => true
                ]
            );

            // 清除快取
            self::clearCache($key);

            return $setting;
        } catch (\Exception $e) {
            Log::error('Failed to set loan setting', [
                'key' => $key,
                'value' => $value,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * 獲取貸款金額設定
     */
    public static function getLoanAmountSettings()
    {
        return [
            'min' => self::get('loan_min_amount', 7000),
            'max' => self::get('loan_max_amount', 100000),
            'default' => self::get('loan_default_amount', 20000),
        ];
    }

    /**
     * 獲取貸款期限設定
     */
    public static function getLoanDaysSettings()
    {
        return [
            'min' => self::get('loan_min_days', 91),
            'max' => self::get('loan_max_days', 365),
        ];
    }

    /**
     * 獲取利率設定
     */
    public static function getLoanRateSettings()
    {
        return [
            'daily_rate' => self::get('loan_daily_rate', 0.03),
        ];
    }

    /**
     * 獲取所有貸款設定
     */
    public static function getAllLoanSettings()
    {
        return [
            'amount' => self::getLoanAmountSettings(),
            'days' => self::getLoanDaysSettings(),
            'rate' => self::getLoanRateSettings(),
        ];
    }

    /**
     * 驗證貸款金額
     */
    public static function validateLoanAmount($amount)
    {
        $settings = self::getLoanAmountSettings();

        if ($amount < $settings['min']) {
            return [
                'valid' => false,
                'message' => "貸款金額不能少於 \${$settings['min']}"
            ];
        }

        if ($amount > $settings['max']) {
            return [
                'valid' => false,
                'message' => "貸款金額不能超過 \${$settings['max']}"
            ];
        }

        return [
            'valid' => true,
            'message' => '貸款金額有效'
        ];
    }

    /**
     * 驗證貸款天數
     */
    public static function validateLoanDays($days)
    {
        $settings = self::getLoanDaysSettings();

        if ($days < $settings['min']) {
            return [
                'valid' => false,
                'message' => "貸款天數不能少於 {$settings['min']} 天"
            ];
        }

        if ($days > $settings['max']) {
            return [
                'valid' => false,
                'message' => "貸款天數不能超過 {$settings['max']} 天"
            ];
        }

        return [
            'valid' => true,
            'message' => '貸款天數有效'
        ];
    }

    /**
     * 計算貸款利息
     */
    public static function calculateInterest($amount, $days)
    {
        $dailyRate = self::get('loan_daily_rate', 0.03) / 100; // 轉換百分比為小數
        $totalInterest = $amount * $dailyRate * $days;
        $totalAmount = $amount + $totalInterest;

        return [
            'principal' => $amount,
            'interest' => round($totalInterest, 2),
            'total' => round($totalAmount, 2),
            'daily_rate' => $dailyRate,
            'days' => $days
        ];
    }

    /**
     * 獲取格式化的金額範圍文字
     */
    public static function getAmountRangeText()
    {
        $settings = self::getLoanAmountSettings();
        return "\${$settings['min']} - \${$settings['max']}";
    }

    /**
     * 獲取格式化的天數範圍文字
     */
    public static function getDaysRangeText()
    {
        $settings = self::getLoanDaysSettings();
        return "{$settings['min']} - {$settings['max']} 天";
    }

    /**
     * 清除特定設定的快取
     */
    public static function clearCache($key = null)
    {
        try {
            if ($key) {
                Cache::forget(self::CACHE_PREFIX . $key);
            } else {
                // 清除所有貸款設定快取
                $keys = ['loan_min_amount', 'loan_max_amount', 'loan_default_amount',
                        'loan_min_days', 'loan_max_days', 'loan_daily_rate'];

                foreach ($keys as $settingKey) {
                    Cache::forget(self::CACHE_PREFIX . $settingKey);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to clear loan setting cache', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * 重新載入所有設定到快取
     */
    public static function warmCache()
    {
        try {
            $settings = LoanSetting::where('is_active', true)->get();

            foreach ($settings as $setting) {
                $cacheKey = self::CACHE_PREFIX . $setting->setting_key;
                $value = self::castValue($setting->setting_value, $setting->setting_type);

                Cache::put($cacheKey, $value, self::CACHE_DURATION);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to warm loan setting cache', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * 根據類型轉換值
     */
    private static function castValue($value, $type)
    {
        switch ($type) {
            case 'integer':
                return (int) $value;
            case 'decimal':
                return (float) $value;
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            default:
                return $value;
        }
    }

    /**
     * 檢查設定是否存在
     */
    public static function exists($key)
    {
        try {
            return LoanSetting::where('setting_key', $key)
                             ->where('is_active', true)
                             ->exists();
        } catch (\Exception $e) {
            Log::error('Failed to check loan setting existence', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * 獲取設定的統計資訊
     */
    public static function getStatistics()
    {
        try {
            return [
                'total_settings' => LoanSetting::count(),
                'active_settings' => LoanSetting::where('is_active', true)->count(),
                'inactive_settings' => LoanSetting::where('is_active', false)->count(),
                'setting_types' => LoanSetting::select('setting_type')
                                             ->groupBy('setting_type')
                                             ->selectRaw('setting_type, count(*) as count')
                                             ->pluck('count', 'setting_type')
                                             ->toArray()
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get loan setting statistics', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }
}
