<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LoanSetting;

class LoanHomeComponent extends Component
{
    public $minAmount;
    public $maxAmount;
    public $defaultAmount;
    public $selectedAmount;
    public $loanCount = 32;

    // 其他設定值
    public $minDays;
    public $maxDays;
    public $dailyRate;

    public function mount()
    {
        // 從資料庫載入設定
        $this->loadLoanSettings();

        // 設定預設選擇金額
        $this->selectedAmount = $this->defaultAmount;
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

    public function updatedSelectedAmount($value)
    {
        // 確保金額在允許範圍內
        if ($value < $this->minAmount) {
            $this->selectedAmount = $this->minAmount;
        } elseif ($value > $this->maxAmount) {
            $this->selectedAmount = $this->maxAmount;
        }
    }

    public function applyLoan()
    {
        // 重定向到申請頁面，並傳遞選擇的金額
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
        $this->loanCount = $this->calculateLoanCount();
    }

    public function render()
    {
        return view('livewire.loan-home-component')->layout('layouts.app');
    }
}
