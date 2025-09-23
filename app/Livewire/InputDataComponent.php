<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LoanApplication;
use Livewire\Attributes\Validate;

class InputDataComponent extends Component
{
    #[Validate('required|string|max:50|regex:/^[\x{4e00}-\x{9fff}a-zA-Z\s]+$/u')]
    public $name = '';

    #[Validate('required|string|regex:/^[0-9]{9,10}$/|unique:loan_applications,phone')]
    public $phone = '';

    #[Validate('required|string|max:100')]
    public $occupation = '';

    #[Validate('required|string|max:50')]
    public $city = '';

    #[Validate('required|string|max:100')]
    public $contact_time = '';

    #[Validate('nullable|string|max:50')]
    public $line_id = '';

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
            // 姓名驗證訊息
            'name.required' => '請輸入姓名',
            'name.max' => '姓名不能超過50個字元',
            'name.regex' => '姓名只能包含中文、英文和空格',

            // 手機驗證訊息
            'phone.required' => '請輸入手機號碼',
            'phone.regex' => '請輸入正確的手機號碼格式（9-10位數字）',
            'phone.unique' => '此手機號碼已申請過貸款',

            // 職業驗證訊息
            'occupation.required' => '請輸入職業',
            'occupation.max' => '職業不能超過100個字元',

            // 縣市驗證訊息
            'city.required' => '請選擇居住縣市',
            'city.max' => '縣市名稱不能超過50個字元',

            // 聯繫時間驗證訊息
            'contact_time.required' => '請選擇方便聯繫時間',
            'contact_time.max' => '聯繫時間不能超過100個字元',

            // Line ID 驗證訊息
            'line_id.max' => 'Line ID 不能超過50個字元',

            // 金額驗證訊息
            'amount.required' => '請選擇貸款金額',
            'amount.numeric' => '貸款金額必須是數字',
            'amount.min' => '貸款金額最少為 $7,000',
            'amount.max' => '貸款金額最多為 $100,000',
        ];
    }

    /**
     * 即時驗證
     */
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    /**
     * 格式化手機號碼輸入
     */
    public function updatedPhone($value)
    {
        // 移除所有非數字字符
        $this->phone = preg_replace('/[^0-9]/', '', $value);

        // 限制長度
        if (strlen($this->phone) > 10) {
            $this->phone = substr($this->phone, 0, 10);
        }
    }

    /**
     * 獲取縣市選項
     */
    public function getCityOptions()
    {
        return [
            '台北市', '新北市', '桃園市', '台中市', '台南市', '高雄市',
            '基隆市', '新竹市', '嘉義市', '新竹縣', '苗栗縣', '彰化縣',
            '南投縣', '雲林縣', '嘉義縣', '屏東縣', '宜蘭縣', '花蓮縣',
            '台東縣', '澎湖縣', '金門縣', '連江縣'
        ];
    }

    /**
     * 獲取聯繫時間選項
     */
    public function getContactTimeOptions()
    {
        return [
            '上午 09:00-12:00',
            '下午 12:00-18:00',
            '晚上 18:00-21:00',
            '平日任何時間',
            '週末任何時間',
            '隨時'
        ];
    }

    /**
     * 提交申請
     */
    public function submitApplication()
    {
        // 驗證所有輸入
        $this->validate();

        try {
            // 創建貸款申請
            $application = LoanApplication::create([
                'name' => trim($this->name),
                'phone' => $this->phone,
                'occupation' => trim($this->occupation),
                'city' => $this->city,
                'contact_time' => $this->contact_time,
                'line_id' => $this->line_id ? trim($this->line_id) : null,
                'amount' => $this->amount,
                'country_code' => '+886',
                'status' => 'pending',
                'applied_at' => now(),
            ]);

            // 清空表單（保留金額）
            $this->reset(['name', 'phone', 'occupation', 'city', 'contact_time', 'line_id']);

            // 顯示成功訊息
            session()->flash('success',
                "您的貸款申請已提交成功！\n" .
                "申請人：" . $application->name . "\n" .
                "申請金額：$" . number_format($this->amount, 0) . "\n" .
                "申請編號：" . str_pad($application->id, 6, '0', STR_PAD_LEFT) . "\n" .
                "我們會在1個工作日內聯繫您。"
            );

            // 記錄成功日誌
            \Log::info('Loan application submitted successfully', [
                'application_id' => $application->id,
                'name' => $application->name,
                'phone' => $application->phone,
                'amount' => $application->amount,
            ]);

            // 可以在這裡添加其他邏輯：
            // 1. 發送確認簡訊給申請人
            // 2. 發送通知給管理員
            // 3. 觸發其他業務流程

        } catch (\Exception $e) {
            // 處理錯誤
            session()->flash('error', '申請提交失敗，請稍後再試或聯繫客服。');

            // 記錄錯誤日誌
            \Log::error('Loan application submission failed', [
                'name' => $this->name,
                'phone' => $this->phone,
                'amount' => $this->amount,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * 重置表單
     */
    public function resetForm()
    {
        $this->reset(['name', 'phone', 'occupation', 'city', 'contact_time', 'line_id']);
        $this->amount = 20000; // 重置為預設金額
    }

    /**
     * 檢查手機號碼是否已存在
     */
    public function checkPhoneExists()
    {
        if ($this->phone && strlen($this->phone) >= 9) {
            $exists = LoanApplication::where('phone', $this->phone)->exists();
            if ($exists) {
                $this->addError('phone', '此手機號碼已申請過貸款');
                return true;
            }
        }
        return false;
    }

    public function render()
    {
        return view('livewire.input-data-component', [
            'cityOptions' => $this->getCityOptions(),
            'contactTimeOptions' => $this->getContactTimeOptions(),
        ])->layout('layouts.app', ['title' => '貸款申請登錄']);
    }
}
