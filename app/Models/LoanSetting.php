<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class LoanSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'setting_key',
        'setting_value',
        'setting_type',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * 根據設定鍵名獲取設定值
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getValue(string $key, $default = null)
    {
        // 使用快取提升效能，快取5分鐘
        $cacheKey = "loan_setting_{$key}";

        return Cache::remember($cacheKey, 300, function () use ($key, $default) {
            $setting = self::where('setting_key', $key)
                          ->where('is_active', true)
                          ->first();

            if (!$setting) {
                return $default;
            }

            return self::castValue($setting->setting_value, $setting->setting_type);
        });
    }

    /**
     * 設定值
     *
     * @param string $key
     * @param mixed $value
     * @param string $type
     * @param string|null $description
     * @return bool
     */
    public static function setValue(string $key, $value, string $type = 'string', ?string $description = null): bool
    {
        $setting = self::updateOrCreate(
            ['setting_key' => $key],
            [
                'setting_value' => (string) $value,
                'setting_type' => $type,
                'description' => $description,
                'is_active' => true
            ]
        );

        // 清除快取
        Cache::forget("loan_setting_{$key}");

        return $setting->wasRecentlyCreated || $setting->wasChanged();
    }

    /**
     * 獲取所有貸款相關設定
     *
     * @return array
     */
    public static function getLoanConfig(): array
    {
        return Cache::remember('loan_config', 300, function () {
            $settings = self::where('is_active', true)
                           ->whereIn('setting_key', [
                               'loan_min_amount',
                               'loan_max_amount',
                               'loan_default_amount',
                               'loan_min_days',
                               'loan_max_days',
                               'loan_daily_rate'
                           ])
                           ->get();

            $config = [];
            foreach ($settings as $setting) {
                $config[$setting->setting_key] = self::castValue(
                    $setting->setting_value,
                    $setting->setting_type
                );
            }

            return $config;
        });
    }

    /**
     * 根據類型轉換值
     *
     * @param string $value
     * @param string $type
     * @return mixed
     */
    protected static function castValue(string $value, string $type)
    {
        return match ($type) {
            'integer' => (int) $value,
            'decimal', 'float' => (float) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'array' => json_decode($value, true),
            'object' => json_decode($value),
            default => $value,
        };
    }

    /**
     * 刷新所有設定快取
     */
    public static function refreshCache(): void
    {
        $settings = self::where('is_active', true)->get();

        foreach ($settings as $setting) {
            Cache::forget("loan_setting_{$setting->setting_key}");
        }

        Cache::forget('loan_config');
    }

    /**
     * 獲取格式化的金額範圍顯示
     *
     * @return string
     */
    public static function getAmountRangeDisplay(): string
    {
        $minAmount = self::getValue('loan_min_amount', 7000);
        $maxAmount = self::getValue('loan_max_amount', 100000);

        return '$' . number_format($minAmount) . '~' . number_format($maxAmount);
    }

    /**
     * 獲取貸款期間顯示
     *
     * @return string
     */
    public static function getLoanPeriodDisplay(): string
    {
        $minDays = self::getValue('loan_min_days', 91);
        $maxDays = self::getValue('loan_max_days', 365);

        return $minDays . '-' . $maxDays . '天';
    }

    /**
     * 獲取日利率顯示
     *
     * @return string
     */
    public static function getDailyRateDisplay(): string
    {
        $rate = self::getValue('loan_daily_rate', 0.03);

        return number_format($rate, 2) . '%/天';
    }

    /**
     * 模型事件：更新或刪除時清除相關快取
     */
    protected static function booted()
    {
        static::saved(function ($setting) {
            Cache::forget("loan_setting_{$setting->setting_key}");
            Cache::forget('loan_config');
        });

        static::deleted(function ($setting) {
            Cache::forget("loan_setting_{$setting->setting_key}");
            Cache::forget('loan_config');
        });
    }
}
