<?php

namespace App\Livewire;

use Livewire\Component;
use App\Helpers\LoanSettingsHelper;

class LoanHomeComponent extends Component
{
    // 貸款金額
    public $amount;
    public $minAmount;
    public $maxAmount;
    public $defaultAmount;

    // 貸款天數
    public $days;
    public $minDays;
    public $maxDays;
    public $defaultDays = 91;

    // 利率資訊
    public $dailyRate;

    // 計算結果
    public $calculatedInterest;
    public $calculatedTotal;

    // UI 狀態
    public $showCalculation = false;

    public function mount()
    {
        // 從設定系統載入參數
        $this->loadSettings();

        // 設定預設值
        $this->amount = $this->defaultAmount;
        $this->days = $this->defaultDays;

        // 初始計算
        $this->calculateLoan();
    }

    /**
     * 載入系統設定
     */
    public function loadSettings()
    {
        try {
            // 載入金額設定
            $amountSettings = LoanSettingsHelper::getLoanAmountSettings();
            $this->minAmount = $amountSettings['min'];
            $this->maxAmount = $amountSettings['max'];
            $this->defaultAmount = $amountSettings['default'];

            // 載入天數設定
            $daysSettings = LoanSettingsHelper::getLoanDaysSettings();
            $this->minDays = $daysSettings['min'];
            $this->maxDays = $daysSettings['max'];

            // 載入利率設定
            $rateSettings = LoanSettingsHelper::getLoanRateSettings();
            $this->dailyRate = $rateSettings['daily_rate'];

        } catch (\Exception $e) {
            // 如果無法載入設定，使用預設值
            $this->minAmount = 7000;
            $this->maxAmount = 100000;
            $this->defaultAmount = 20000;
            $this->minDays = 91;
            $this->maxDays = 365;
            $this->dailyRate = 0.03;

            \Log::warning('Failed to load loan settings, using defaults', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * 更新金額時的處理
     */
    public function updatedAmount($value)
    {
        // 驗證金額範圍
        if ($value < $this->minAmount) {
            $this->amount = $this->minAmount;
        } elseif ($value > $this->maxAmount) {
            $this->amount = $this->maxAmount;
        }

        $this->calculateLoan();
    }

    /**
     * 更新天數時的處理
     */
    public function updatedDays($value)
    {
        // 驗證天數範圍
        if ($value < $this->minDays) {
            $this->days = $this->minDays;
        } elseif ($value > $this->maxDays) {
            $this->days = $this->maxDays;
        }

        $this->calculateLoan();
    }

    /**
     * 計算貸款
     */
    public function calculateLoan()
    {
        try {
            $result = LoanSettingsHelper::calculateInterest($this->amount, $this->days);

            $this->calculatedInterest = $result['interest'];
            $this->calculatedTotal = $result['total'];
            $this->showCalculation = true;

        } catch (\Exception $e) {
            // 簡單計算作為後備方案
            $dailyRate = $this->dailyRate / 100;
            $this->calculatedInterest = round($this->amount * $dailyRate * $this->days, 2);
            $this->calculatedTotal = $this->amount + $this->calculatedInterest;
            $this->showCalculation = true;

            \Log::warning('Failed to calculate loan using helper, using simple calculation', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * 設定常用金額
     */
    public function setQuickAmount($amount)
    {
        // 驗證金額是否在範圍內
        $validation = LoanSettingsHelper::validateLoanAmount($amount);

        if ($validation['valid']) {
            $this->amount = $amount;
            $this->calculateLoan();
        } else {
            session()->flash('error', $validation['message']);
        }
    }

    /**
     * 重置為預設值
     */
    public function resetToDefaults()
    {
        $this->amount = $this->defaultAmount;
        $this->days = $this->defaultDays;
        $this->calculateLoan();

        session()->flash('message', '已重置為預設值');
    }

    /**
     * 前往申請頁面
     */
    public function goToApplication()
    {
        // 最終驗證
        $amountValidation = LoanSettingsHelper::validateLoanAmount($this->amount);
        $daysValidation = LoanSettingsHelper::validateLoanDays($this->days);

        if (!$amountValidation['valid']) {
            session()->flash('error', $amountValidation['message']);
            return;
        }

        if (!$daysValidation['valid']) {
            session()->flash('error', $daysValidation['message']);
            return;
        }

        return redirect()->route('loan.apply', [
            'amount' => $this->amount,
            'days' => $this->days
        ]);
    }

    /**
     * 獲取金額範圍文字
     */
    public function getAmountRangeTextProperty()
    {
        return LoanSettingsHelper::getAmountRangeText();
    }

    /**
     * 獲取天數範圍文字
     */
    public function getDaysRangeTextProperty()
    {
        return LoanSettingsHelper::getDaysRangeText();
    }

    /**
     * 獲取常用金額選項
     */
    public function getQuickAmountOptionsProperty()
    {
        $quickAmounts = [];

        // 基於設定動態生成常用金額
        $step = ($this->maxAmount - $this->minAmount) / 6;

        for ($i = 0; $i < 6; $i++) {
            $amount = $this->minAmount + ($step * $i);
            // 圓整到千位
            $amount = round($amount / 1000) * 1000;

            if ($amount >= $this->minAmount && $amount <= $this->maxAmount) {
                $quickAmounts[] = $amount;
            }
        }

        // 確保包含預設金額
        if (!in_array($this->defaultAmount, $quickAmounts)) {
            $quickAmounts[] = $this->defaultAmount;
            sort($quickAmounts);
        }

        return array_unique($quickAmounts);
    }

    /**
     * 格式化金額顯示
     */
    public function formatAmount($amount)
    {
        return number_format($amount, 0);
    }

    /**
     * 格式化百分比顯示
     */
    public function formatRate($rate)
    {
        return number_format($rate, 2);
    }

    public function render()
    {
        return view('livewire.loan-home-component', [
            'amountRangeText' => $this->getAmountRangeTextProperty(),
            'daysRangeText' => $this->getDaysRangeTextProperty(),
            'quickAmountOptions' => $this->getQuickAmountOptionsProperty(),
        ]);
    }
}
