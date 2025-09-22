<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LoanApplication;
use Livewire\Attributes\Validate;

class InputDataComponent extends Component
{
    #[Validate('required|string|regex:/^[0-9]{9,10}$/|unique:loan_applications,phone')]
    public $phone = '';

    #[Validate('required|numeric|min:7000|max:100000')]
    public $amount = 20000;

    public function mount()
    {
        // 從 session 或 query parameter 獲取金額
        $this->amount = session('loan_amount', request('amount', 20000));
    }

    protected function messages()
    {
        return [
            'phone.required' => '請輸入手機號碼',
            'phone.regex' => '請輸入正確的手機號碼格式（9-10位數字）',
            'phone.unique' => '此手機號碼已申請過貸款',
            'amount.required' => '請選擇貸款金額',
            'amount.min' => '貸款金額最少為 $7,000',
            'amount.max' => '貸款金額最多為 $100,000',
        ];
    }

    public function submitApplication()
    {
        // 驗證輸入
        $this->validate();

        try {
            // 創建貸款申請
            $application = LoanApplication::create([
                'phone' => $this->phone,
                'amount' => $this->amount,
                'country_code' => '+886',
                'status' => 'pending',
                'applied_at' => now(),
            ]);

            // 清空表單
            $this->reset('phone');

            // 顯示成功訊息
            session()->flash('success',
                "您的貸款申請已提交成功！\n" .
                "申請金額：$" . number_format($this->amount, 0) . "\n" .
                "申請編號：" . str_pad($application->id, 6, '0', STR_PAD_LEFT) . "\n" .
                "我們會盡快處理您的申請。"
            );

            // 可以在這裡添加其他邏輯，如發送通知給管理員等

        } catch (\Exception $e) {
            // 處理錯誤
            session()->flash('error', '申請提交失敗，請稍後再試。');

            // 記錄錯誤日誌
            \Log::error('Loan application submission failed', [
                'phone' => $this->phone,
                'amount' => $this->amount,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.input-data-component')
                    ->layout('layouts.app', ['title' => '貸款申請登錄']);
    }
}
