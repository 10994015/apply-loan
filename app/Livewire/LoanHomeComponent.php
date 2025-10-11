<?php

namespace App\Livewire;

use App\Models\LoanApplication;
use Livewire\Component;
use App\Models\LoanSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class LoanHomeComponent extends Component
{
    public $minAmount;
    public $maxAmount;
    public $defaultAmount;
    public $selectedAmount;
    public $loanCount = 0;

    // 其他設定值
    public $minDays;
    public $maxDays;
    public $dailyRate;

    // 錯誤訊息
    public $errorMessage = '';
    public $showError = false;

    public function mount()
    {
        // 從資料庫載入設定
        $this->loadLoanSettings();

        // 設定預設選擇金額
        $this->selectedAmount = $this->defaultAmount;

        //抓取今日申請人數
        $this->loanCount = LoanApplication::whereDate('applied_at', Carbon::today())->count();
        $this->loanCount = ($this->loanCount+3)*12;
    }

    public function loadLoanSettings()
    {
        // 取得所有貸款設定
        $config = LoanSetting::getLoanConfig();

        $this->minAmount = $config['loan_min_amount'] ?? 7000;
        $this->maxAmount = $config['loan_max_amount'] ?? 100000;
        $this->defaultAmount = $config['loan_default_amount'] ?? 20000;
        $this->minDays = $config['loan_min_days'] ?? 91;
        $this->maxDays = $config['loan_max_days'] ?? 365;
        $this->dailyRate = $config['loan_daily_rate'] ?? 0.03;
    }

    /**
     * 當選擇金額更新時驗證和格式化
     */
    public function updatedSelectedAmount($value)
    {
        // 移除逗號和非數字字元
        $value = preg_replace('/[^\d]/', '', $value);
        $value = (int) $value;

        // 清除之前的錯誤訊息
        $this->showError = false;
        $this->errorMessage = '';

        // 確保金額在允許範圍內
        if ($value < $this->minAmount) {
            $this->selectedAmount = $this->minAmount;
        } elseif ($value > $this->maxAmount) {
            $this->selectedAmount = $this->maxAmount;
        } else {
            // 調整到最接近的千位數
            $this->selectedAmount = round($value / 1000) * 1000;
        }
    }

    /**
     * 設定金額（用於快速選擇按鈕）
     */
    public function setAmount($amount)
    {
        $this->selectedAmount = $amount;
        $this->showError = false;
        $this->errorMessage = '';
    }

    /**
     * 驗證金額是否有效
     */
    public function validateAmount()
    {
        // 確保金額是數字
        $amount = (int) $this->selectedAmount;

        // 檢查是否為空或無效
        if (empty($amount) || $amount <= 0) {
            $this->showError = true;
            $this->errorMessage = '請選擇申請金額';
            $this->dispatch('show-error-toast');
            return false;
        }

        // 檢查是否低於最小金額
        if ($amount < $this->minAmount) {
            $this->showError = true;
            $this->errorMessage = "申請金額不能低於 $" . number_format($this->minAmount);
            $this->selectedAmount = $this->minAmount;
            $this->dispatch('show-error-toast');
            return false;
        }

        // 檢查是否超過最大金額
        if ($amount > $this->maxAmount) {
            $this->showError = true;
            $this->errorMessage = "申請金額不能超過 $" . number_format($this->maxAmount);
            $this->selectedAmount = $this->maxAmount;
            $this->dispatch('show-error-toast');
            return false;
        }

        return true;
    }

    /**
     * 申請貸款
     */
    public function applyLoan()
    {
        // 驗證金額
        if (!$this->validateAmount()) {
            // 驗證失敗，不允許進入申請頁面
            Log::warning('Loan application blocked - Invalid amount', [
                'selected_amount' => $this->selectedAmount,
                'min_amount' => $this->minAmount,
                'max_amount' => $this->maxAmount
            ]);
            return;
        }

        // 記錄申請
        Log::info('User proceeding to loan application', [
            'amount' => $this->selectedAmount,
            'timestamp' => now()
        ]);

        // 驗證成功，重定向到申請頁面
        return redirect()->route('loan.apply', ['amount' => $this->selectedAmount]);
    }

    public function getAmountRangeDisplay()
    {
        return '$' . number_format($this->minAmount) . '~' . number_format($this->maxAmount);
    }

    public function getLoanPeriodDisplay()
    {
        return $this->minDays . '-' . $this->maxDays . '天';
    }

    public function getDailyRateDisplay()
    {
        return number_format($this->dailyRate, 2) . '%/天';
    }

    public function calculateLoanCount()
    {
        $now = new \DateTime();
        $hours = (int)$now->format('H');
        $minutes = (int)$now->format('i');

        // 基數：32人（上午9點的基數）
        $baseCount = 32;

        // 計算從上午9點開始到現在的總分鐘數
        $minutesFromStart = 0;

        if ($hours >= 9) {
            $minutesFromStart = ($hours - 9) * 60 + $minutes;
        } else {
            // 如果是早上9點前，顯示前一天最終數據
            $minutesFromStart = 0;
            $baseCount = 380 + rand(0, 19); // 前一天的數據
        }

        // 每小時平均增加12-18人，轉換為每分鐘0.2-0.3人
        $incrementPerMinute = 0.2 + (mt_rand(0, 100) / 1000);
        $totalIncrement = floor($minutesFromStart * $incrementPerMinute);

        // 加入一些隨機波動
        $randomFactor = rand(-5, 5);

        return max($baseCount + $totalIncrement + $randomFactor, $baseCount);
    }

    public function updateLoanCount()
    {
        // 可以在這裡實作更新邏輯
    }

    public function render()
    {
        return view('livewire.loan-home-component')->layout('layouts.app');
    }
}
