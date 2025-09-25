<?php

namespace App\Providers;

use App\Helpers\LoanSettingsHelper;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

class LoanSettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // 註冊 LoanSettingsHelper 為單例
        $this->app->singleton('loan.settings', function ($app) {
            return new LoanSettingsHelper();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // 監聽模型事件來自動清除快取
        $this->registerModelEvents();

        // 在應用啟動時預熱快取（可選）
        if ($this->app->runningInConsole()) {
            // 在 console 命令中不預熱快取
            return;
        }

        // 延遲預熱快取，避免在 migration 時出錯
        $this->app->booted(function () {
            try {
                // 檢查表是否存在
                if (\Schema::hasTable('loan_settings')) {
                    LoanSettingsHelper::warmCache();
                }
            } catch (\Exception $e) {
                // 靜默處理錯誤，避免在 migration 階段中斷
            }
        });
    }

    /**
     * 註冊模型事件監聽器
     */
    protected function registerModelEvents()
    {
        // 當 LoanSetting 模型更新、創建或刪除時清除快取
        Event::listen('eloquent.saved: App\Models\LoanSetting', function ($setting) {
            LoanSettingsHelper::clearCache($setting->setting_key);
        });

        Event::listen('eloquent.deleted: App\Models\LoanSetting', function ($setting) {
            LoanSettingsHelper::clearCache($setting->setting_key);
        });

        // 當批量操作時清除所有快取
        Event::listen('eloquent.updated: App\Models\LoanSetting', function () {
            LoanSettingsHelper::clearCache();
        });
    }
}
