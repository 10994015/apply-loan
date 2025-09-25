<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\LoanApplication;
use App\Models\LoanSetting; // 添加這個 import
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Url;

class InputDataComponent extends Component
{
    use WithFileUploads;

    // 步驟追蹤
    public $currentStep = 1;
    public $step1Completed = false;
    public $step2Completed = false;
    public $step3Completed = false;
    public $step4Completed = false;
    public $step5Completed = false;

    // 步驟1：基本資料
    #[Validate('required|string|max:50|regex:/^[\x{4e00}-\x{9fff}a-zA-Z\s]+$/u')]
    public $name = '';

    #[Validate('required|string|regex:/^[0-9]{9,10}$/')]
    public $phone = '';

    #[Validate('required|string|max:100')]
    public $occupation = '';

    #[Validate('required|string|max:50')]
    public $city = '';

    #[Validate('required|string|max:200')]
    public $address = '';

    #[Validate('required|string|max:100')]
    public $contact_time = '';

    #[Validate('nullable|string|max:50')]
    public $line_id = '';

    #[Url(as: 'amount')]
    public $amount = 20000;

    // 步驟2：緊急聯絡人
    #[Validate('required|string|max:50|regex:/^[\x{4e00}-\x{9fff}a-zA-Z\s]+$/u')]
    public $emergency_contact_1_name = '';

    #[Validate('required|string|regex:/^[0-9]{9,10}$/')]
    public $emergency_contact_1_phone = '';

    #[Validate('required|string|in:父親,母親,兄弟,姊妹')]
    public $emergency_contact_1_relationship = '';

    #[Validate('required|string|max:50|regex:/^[\x{4e00}-\x{9fff}a-zA-Z\s]+$/u')]
    public $emergency_contact_2_name = '';

    #[Validate('required|string|regex:/^[0-9]{9,10}$/')]
    public $emergency_contact_2_phone = '';

    #[Validate('required|string|in:父親,母親,兄弟,姊妹')]
    public $emergency_contact_2_relationship = '';

    // 步驟3：證件上傳
    #[Validate('required|image|max:10240')]
    public $id_card_front = null;

    #[Validate('required|image|max:10240')]
    public $id_card_back = null;

    #[Validate('required|image|max:10240')]
    public $id_card_selfie = null;

    #[Validate('required|image|max:10240')]
    public $second_document = null;

    // 步驟4：銀行資訊（選填）
    #[Validate('nullable|image|max:10240')]
    public $bank_card = null;

    // 申請ID（只有在最後才產生）
    public $applicationId = null;

    public function mount()
    {
        // 從資料庫載入金額設定
        $minAmount = LoanSetting::getValue('loan_min_amount', 7000);
        $maxAmount = LoanSetting::getValue('loan_max_amount', 100000);
        $defaultAmount = LoanSetting::getValue('loan_default_amount', 20000);



        // 驗證金額在允許範圍內
        if ($this->amount < $minAmount) {
            $this->amount = $minAmount;
        } elseif ($this->amount > $maxAmount) {
            $this->amount = $maxAmount;
        }

        // 恢復之前的進度（如果有的話）
        $this->restoreProgressFromSession();

        // 如果進入頁面時已經是第五步驟，自動開始新申請
        if ($this->currentStep == 5) {
            \Log::info('User entered at step 5, starting new application automatically');
            $this->startNewApplication();
        }
    }

    public function getLoanAmountLimits()
    {
        return [
            'min' => LoanSetting::getValue('loan_min_amount', 7000),
            'max' => LoanSetting::getValue('loan_max_amount', 100000),
            'default' => LoanSetting::getValue('loan_default_amount', 20000)
        ];
    }


    protected function messages()
    {
        $limits = $this->getLoanAmountLimits();

        return [
            // 基本資料驗證訊息
            'name.required' => '請輸入姓名',
            'name.max' => '姓名不能超過50個字元',
            'name.regex' => '姓名只能包含中文、英文和空格',

            'phone.required' => '請輸入手機號碼',
            'phone.regex' => '請輸入正確的手機號碼格式（9-10位數字）',

            'occupation.required' => '請輸入職業',
            'occupation.max' => '職業不能超過100個字元',

            'city.required' => '請選擇居住縣市',
            'city.max' => '縣市名稱不能超過50個字元',

            'address.required' => '請輸入詳細地址',
            'address.max' => '地址不能超過200個字元',

            'contact_time.required' => '請選擇方便聯繫時間',
            'contact_time.max' => '聯繫時間不能超過100個字元',

            'line_id.max' => 'Line ID 不能超過50個字元',

            'amount.required' => '請選擇貸款金額',
            'amount.numeric' => '貸款金額必須是數字',
            'amount.min' => '貸款金額最少為 $' . number_format($limits['min']),
            'amount.max' => '貸款金額最多為 $' . number_format($limits['max']),

            // 其他驗證訊息保持不變...
            'emergency_contact_1_name.required' => '請輸入第一位緊急聯絡人姓名',
            'emergency_contact_1_name.regex' => '姓名只能包含中文、英文和空格',
            'emergency_contact_1_phone.required' => '請輸入第一位緊急聯絡人手機號碼',
            'emergency_contact_1_phone.regex' => '請輸入正確的手機號碼格式',
            'emergency_contact_1_relationship.required' => '請選擇第一位緊急聯絡人關係',
            'emergency_contact_1_relationship.in' => '關係只能選擇父親、母親、兄弟或姊妹',

            'emergency_contact_2_name.required' => '請輸入第二位緊急聯絡人姓名',
            'emergency_contact_2_name.regex' => '姓名只能包含中文、英文和空格',
            'emergency_contact_2_phone.required' => '請輸入第二位緊急聯絡人手機號碼',
            'emergency_contact_2_phone.regex' => '請輸入正確的手機號碼格式',
            'emergency_contact_2_relationship.required' => '請選擇第二位緊急聯絡人關係',
            'emergency_contact_2_relationship.in' => '關係只能選擇父親、母親、兄弟或姊妹',

            // 證件上傳驗證訊息
            'id_card_front.required' => '請上傳身分證正面',
            'id_card_front.image' => '身分證正面必須是圖片檔案',
            'id_card_front.max' => '身分證正面圖片不能超過10MB',

            'id_card_back.required' => '請上傳身分證反面',
            'id_card_back.image' => '身分證反面必須是圖片檔案',
            'id_card_back.max' => '身分證反面圖片不能超過10MB',

            'id_card_selfie.required' => '請上傳手持身分證自拍',
            'id_card_selfie.image' => '手持身分證自拍必須是圖片檔案',
            'id_card_selfie.max' => '手持身分證自拍圖片不能超過10MB',

            'second_document.required' => '請上傳第二證件',
            'second_document.image' => '第二證件必須是圖片檔案',
            'second_document.max' => '第二證件圖片不能超過10MB',

            // 銀行卡驗證訊息
            'bank_card.image' => '銀行卡圖片必須是圖片檔案',
            'bank_card.max' => '銀行卡圖片不能超過10MB',
        ];
    }


    /**
     * 移除檔案
     */
    public function removeFile($propertyName)
    {
        try {
            // 檢查屬性是否存在且有值
            if (property_exists($this, $propertyName) && $this->$propertyName) {
                \Log::info('Removing file', ['property' => $propertyName]);

                // 重置屬性為 null
                $this->$propertyName = null;

                // 清除驗證錯誤
                $this->resetErrorBag($propertyName);

                // 更新 session
                $this->saveProgressToSession();

                // 觸發前端事件
                $this->dispatch('file-removed', property: $propertyName);

                \Log::info('File removed successfully', ['property' => $propertyName]);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to remove file', [
                'property' => $propertyName,
                'error' => $e->getMessage()
            ]);

            session()->flash('error', '移除檔案失敗，請重試');
        }
    }

    /**
     * 下一步 - 驗證並進行下一步
     */
    public function nextStep()
    {
        \Log::info('nextStep started', ['step' => $this->currentStep]);

        try {
            switch ($this->currentStep) {
                case 1:
                    $this->validateStep1();
                    $this->step1Completed = true;
                    $this->currentStep = 2;
                    break;

                case 2:
                    $this->validateStep2();
                    $this->step2Completed = true;
                    $this->currentStep = 3;
                    break;

                case 3:
                    $this->validateStep3();
                    $this->step3Completed = true;
                    $this->currentStep = 4;
                    break;

                case 4:
                    $this->step4Completed = true;
                    $this->currentStep = 5;
                    $this->completeApplication(); // 在這裡才真正寫入資料庫和上傳到S3
                    break;
            }

            // 每一步都更新 session
            $this->saveProgressToSession();

            // 觸發前端事件
            $this->dispatch('step-changed', step: $this->currentStep);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', ['errors' => $e->errors()]);
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Step processing failed', [
                'step' => $this->currentStep,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', '處理失敗，請稍後再試：' . $e->getMessage());
        }
    }

    /**
     * 上一步
     */
    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
            $this->saveProgressToSession();
            $this->dispatch('step-changed', step: $this->currentStep);
        }
    }

    /**
     * 跳過步驟（僅限步驟4）
     */
    public function skipStep()
    {
        if ($this->currentStep == 4) {
            $this->step4Completed = true;
            $this->currentStep = 5;
            $this->completeApplication(); // 在這裡才真正寫入資料庫和上傳到S3
            $this->saveProgressToSession();
            $this->dispatch('step-changed', step: $this->currentStep);
        }
    }

    /**
     * 驗證步驟1
     */
    private function validateStep1()
    {
        $limits = $this->getLoanAmountLimits();

        $this->validate([
            'name' => 'required|string|max:50|regex:/^[\x{4e00}-\x{9fff}a-zA-Z\s]+$/u',
            'phone' => 'required|string|regex:/^[0-9]{9,10}$/',
            'occupation' => 'required|string|max:100',
            'city' => 'required|string|max:50',
            'address' => 'required|string|max:200',
            'contact_time' => 'required|string|max:100',
            'line_id' => 'nullable|string|max:50',
            'amount' => "required|numeric|min:{$limits['min']}|max:{$limits['max']}",
        ]);
    }


    /**
     * 驗證步驟2
     */
    private function validateStep2()
    {
        $this->validate([
            'emergency_contact_1_name' => 'required|string|max:50|regex:/^[\x{4e00}-\x{9fff}a-zA-Z\s]+$/u',
            'emergency_contact_1_phone' => 'required|string|regex:/^[0-9]{9,10}$/',
            'emergency_contact_1_relationship' => 'required|string|in:父親,母親,兄弟,姊妹',
            'emergency_contact_2_name' => 'required|string|max:50|regex:/^[\x{4e00}-\x{9fff}a-zA-Z\s]+$/u',
            'emergency_contact_2_phone' => 'required|string|regex:/^[0-9]{9,10}$/',
            'emergency_contact_2_relationship' => 'required|string|in:父親,母親,兄弟,姊妹',
        ]);

        // 檢查兩個緊急聯絡人不能是同一個人
        if ($this->emergency_contact_1_phone == $this->emergency_contact_2_phone) {
            throw new \Exception('兩位緊急聯絡人不能是同一人');
        }

        // 檢查緊急聯絡人手機不能和申請人相同
        if ($this->emergency_contact_1_phone == $this->phone || $this->emergency_contact_2_phone == $this->phone) {
            throw new \Exception('緊急聯絡人手機號碼不能與申請人相同');
        }
    }

    /**
     * 驗證步驟3
     */
    private function validateStep3()
    {
        $this->validate([
            'id_card_front' => 'required|image|max:10240',
            'id_card_back' => 'required|image|max:10240',
            'id_card_selfie' => 'required|image|max:10240',
            'second_document' => 'required|image|max:10240',
        ]);
    }

    /**
     * 完成申請 - 這裡才真正寫入資料庫並上傳檔案到S3
     */
    private function completeApplication()
    {
        try {
            \Log::info('Starting final application submission', [
                'session_id' => session()->getId(),
                'name' => $this->name,
                'phone' => $this->phone
            ]);

            // 開始資料庫事務，確保資料一致性
            \DB::beginTransaction();

            // 1. 創建申請記錄（先不設定檔案路徑）
            $application = LoanApplication::create([
                // 基本資料
                'name' => trim($this->name),
                'phone' => $this->phone,
                'occupation' => trim($this->occupation),
                'city' => $this->city,
                'address' => trim($this->address),
                'contact_time' => $this->contact_time,
                'line_id' => $this->line_id ? trim($this->line_id) : null,
                'amount' => $this->amount,
                'country_code' => '+886',

                // 緊急聯絡人
                'emergency_contact_1_name' => trim($this->emergency_contact_1_name),
                'emergency_contact_1_phone' => $this->emergency_contact_1_phone,
                'emergency_contact_1_relationship' => $this->emergency_contact_1_relationship,
                'emergency_contact_2_name' => trim($this->emergency_contact_2_name),
                'emergency_contact_2_phone' => $this->emergency_contact_2_phone,
                'emergency_contact_2_relationship' => $this->emergency_contact_2_relationship,

                // 狀態和時間戳記
                'status' => 'pending',
                'current_step' => 5,
                'step_1_completed' => true,
                'step_1_completed_at' => now(),
                'step_2_completed' => true,
                'step_2_completed_at' => now(),
                'step_3_completed' => true,
                'step_3_completed_at' => now(),
                'step_4_completed' => true,
                'step_4_completed_at' => now(),
                'step_5_completed' => true,
                'step_5_completed_at' => now(),
                'applied_at' => now(),

                // 暫時設置為 null，稍後更新檔案路徑
                'id_card_front_path' => null,
                'id_card_back_path' => null,
                'id_card_selfie_path' => null,
                'second_document_path' => null,
                'bank_card_path' => null,
            ]);

            if (!$application) {
                throw new \Exception('創建申請記錄失敗');
            }

            $this->applicationId = $application->id;
            \Log::info('Application record created', ['application_id' => $this->applicationId]);

            // 2. 上傳檔案到S3並獲取檔案路徑
            $filePaths = $this->uploadFilesToS3($application->id);

            // 3. 更新申請記錄的檔案路徑
            $application->update([
                'id_card_front_path' => $filePaths['id_card_front'] ?? null,
                'id_card_back_path' => $filePaths['id_card_back'] ?? null,
                'id_card_selfie_path' => $filePaths['id_card_selfie'] ?? null,
                'second_document_path' => $filePaths['second_document'] ?? null,
                'bank_card_path' => $filePaths['bank_card'] ?? null,
            ]);

            // 4. 提交事務
            \DB::commit();

            // 5. 設置完成狀態
            $this->step5Completed = true;

            // 6. 立即清理所有暫存資料，讓用戶可以填寫下一份申請
            $this->clearAllTemporaryData();

            // 7. 記錄成功日誌
            \Log::info('Loan application completed successfully', [
                'application_id' => $this->applicationId,
                'name' => $this->name,
                'phone' => $this->phone,
                'amount' => $this->amount,
                'files_uploaded' => [
                    'id_card_front' => !empty($filePaths['id_card_front']),
                    'id_card_back' => !empty($filePaths['id_card_back']),
                    'id_card_selfie' => !empty($filePaths['id_card_selfie']),
                    'second_document' => !empty($filePaths['second_document']),
                    'bank_card' => !empty($filePaths['bank_card']),
                ]
            ]);

            // 8. 觸發後續業務邏輯（可選）
            $this->triggerPostSubmissionActions($application);

        } catch (\Exception $e) {
            // 回滾事務
            \DB::rollBack();

            \Log::error('Failed to complete application', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'session_id' => session()->getId(),
                'name' => $this->name,
                'phone' => $this->phone
            ]);

            // 重新拋出異常，讓上層處理
            throw new \Exception('申請提交失敗，請重試：' . $e->getMessage());
        }
    }

    /**
     * 上傳檔案到S3
     */
    private function uploadFilesToS3($applicationId)
    {
        $filePaths = [];

        try {
            // 檔案對應關係
            $fileMapping = [
                'id_card_front' => $this->id_card_front,
                'id_card_back' => $this->id_card_back,
                'id_card_selfie' => $this->id_card_selfie,
                'second_document' => $this->second_document,
                'bank_card' => $this->bank_card,
            ];

            foreach ($fileMapping as $fileType => $file) {
                if ($file && $file->isValid()) {
                    try {
                        // 生成檔案名（包含申請ID和時間戳）
                        $extension = $file->getClientOriginalExtension();
                        $filename = $applicationId . '_' . $fileType . '_' . time() . '.' . $extension;

                        // 上傳到S3的loan_documents目錄
                        $path = $file->storeAs(
                            'loan_documents/' . $applicationId,
                            $filename,
                            's3' // 使用s3 disk
                        );

                        if ($path) {
                            $filePaths[$fileType] = $path;

                            \Log::info('File uploaded to S3 successfully', [
                                'file_type' => $fileType,
                                'filename' => $filename,
                                'path' => $path,
                                'application_id' => $applicationId
                            ]);
                        } else {
                            throw new \Exception("上傳檔案到S3失敗: {$fileType}");
                        }

                    } catch (\Exception $e) {
                        \Log::error('Error uploading individual file to S3', [
                            'file_type' => $fileType,
                            'error' => $e->getMessage(),
                            'application_id' => $applicationId
                        ]);
                        throw $e;
                    }
                }
            }

            \Log::info('All files uploaded to S3 successfully', [
                'application_id' => $applicationId,
                'uploaded_files' => array_keys($filePaths)
            ]);

            return $filePaths;

        } catch (\Exception $e) {
            \Log::error('Failed to upload files to S3', [
                'application_id' => $applicationId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function clearAllTemporaryData()
    {
        try {
            \Log::info('Starting to clear all temporary data');

            // 1. 清理 session 資料
            session()->forget([
                'loan_application_progress',
                'loan_amount'
            ]);

            // 2. 重置 component 屬性（保留 applicationId 和 step5Completed 用於顯示成功頁面）
            $this->resetComponentPropertiesForNewApplication();

            \Log::info('All temporary data cleared successfully');

        } catch (\Exception $e) {
            \Log::error('Failed to clear all temporary data', [
                'error' => $e->getMessage()
            ]);
            // 清理失敗不應該影響主流程，只記錄錯誤
        }
    }

    /**
     * 重置 component 屬性為新申請準備
     */
    private function resetComponentPropertiesForNewApplication()
    {
        // 重置基本資料
        $this->name = '';
        $this->phone = '';
        $this->occupation = '';
        $this->city = '';
        $this->address = '';
        $this->contact_time = '';
        $this->line_id = '';
        $this->amount = LoanSetting::getValue('loan_default_amount', 20000); // 使用動態預設值

        // 重置緊急聯絡人
        $this->emergency_contact_1_name = '';
        $this->emergency_contact_1_phone = '';
        $this->emergency_contact_1_relationship = '';
        $this->emergency_contact_2_name = '';
        $this->emergency_contact_2_phone = '';
        $this->emergency_contact_2_relationship = '';

        // 重置檔案
        $this->id_card_front = null;
        $this->id_card_back = null;
        $this->id_card_selfie = null;
        $this->second_document = null;
        $this->bank_card = null;

        // 重置步驟狀態（除了當前步驟和完成狀態，用於顯示成功頁面）
        $this->step1Completed = false;
        $this->step2Completed = false;
        $this->step3Completed = false;
        $this->step4Completed = false;

        // 保留 currentStep = 5 和 step5Completed = true 以顯示成功頁面
        // 保留 applicationId 以顯示申請編號
    }


    /**
     * 觸發申請提交後的業務邏輯
     */
    private function triggerPostSubmissionActions($application)
    {
        try {
            // 1. 發送確認通知（可以用 Queue 處理）
            // dispatch(new SendApplicationConfirmationJob($application));

            // 2. 通知管理員有新申請（可以用 Queue 處理）
            // dispatch(new NotifyAdminNewApplicationJob($application));

            // 3. 觸發審核流程（可以用 Queue 處理）
            // dispatch(new StartApplicationReviewJob($application));

            // 4. 記錄業務事件
            \Log::info('Post submission actions triggered', [
                'application_id' => $application->id
            ]);

        } catch (\Exception $e) {
            // 後續業務邏輯失敗不應該影響主申請流程
            \Log::error('Post submission actions failed', [
                'application_id' => $application->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * 開始新申請
     */
    public function startNewApplication()
    {
        try {
            \Log::info('Starting new application', [
                'previous_application_id' => $this->applicationId,
                'session_id' => session()->getId()
            ]);

            // 1. 完全重置所有屬性
            $this->resetForm();

            // 2. 重置步驟到第一步
            $this->currentStep = 1;
            $this->step1Completed = false;
            $this->step2Completed = false;
            $this->step3Completed = false;
            $this->step4Completed = false;
            $this->step5Completed = false;
            $this->applicationId = null;

            // 3. 重置檔案
            $this->id_card_front = null;
            $this->id_card_back = null;
            $this->id_card_selfie = null;
            $this->second_document = null;
            $this->bank_card = null;

            // 4. 重置金額為從資料庫取得的預設值
            $this->amount = LoanSetting::getValue('loan_default_amount', 20000);

            // 5. 清除驗證錯誤
            $this->resetErrorBag();

            // 6. 清除 session 資料
            session()->forget([
                'loan_application_progress',
                'loan_amount'
            ]);

            // 7. 觸發前端事件
            $this->dispatch('new-application-started');
            $this->dispatch('step-changed', step: 1);

            \Log::info('New application started successfully');

        } catch (\Exception $e) {
            \Log::error('Failed to start new application', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', '開始新申請失敗，請重新整理頁面再試');
        }
    }


    /**
     * 保存目前進度到 session
     */
    public function saveProgressToSession()
    {
        session()->put('loan_application_progress', [
            'current_step' => $this->currentStep,
            'basic_data' => [
                'name' => $this->name,
                'phone' => $this->phone,
                'occupation' => $this->occupation,
                'city' => $this->city,
                'address' => $this->address,
                'contact_time' => $this->contact_time,
                'line_id' => $this->line_id,
                'amount' => $this->amount,
            ],
            'emergency_contacts' => [
                'contact_1' => [
                    'name' => $this->emergency_contact_1_name,
                    'phone' => $this->emergency_contact_1_phone,
                    'relationship' => $this->emergency_contact_1_relationship,
                ],
                'contact_2' => [
                    'name' => $this->emergency_contact_2_name,
                    'phone' => $this->emergency_contact_2_phone,
                    'relationship' => $this->emergency_contact_2_relationship,
                ]
            ],
            'step_completed' => [
                'step1' => $this->step1Completed,
                'step2' => $this->step2Completed,
                'step3' => $this->step3Completed,
                'step4' => $this->step4Completed,
                'step5' => $this->step5Completed,
            ]
        ]);
    }

    /**
     * 從 session 恢復進度
     */
    public function restoreProgressFromSession()
    {
        $progress = session('loan_application_progress');

        if ($progress) {
            // 恢復基本資料
            if (isset($progress['basic_data'])) {
                $this->name = $progress['basic_data']['name'] ?? '';
                $this->phone = $progress['basic_data']['phone'] ?? '';
                $this->occupation = $progress['basic_data']['occupation'] ?? '';
                $this->city = $progress['basic_data']['city'] ?? '';
                $this->address = $progress['basic_data']['address'] ?? '';
                $this->contact_time = $progress['basic_data']['contact_time'] ?? '';
                $this->line_id = $progress['basic_data']['line_id'] ?? '';
                $this->amount = $progress['basic_data']['amount'] ?? 20000;
            }

            // 恢復緊急聯絡人資料
            if (isset($progress['emergency_contacts'])) {
                $this->emergency_contact_1_name = $progress['emergency_contacts']['contact_1']['name'] ?? '';
                $this->emergency_contact_1_phone = $progress['emergency_contacts']['contact_1']['phone'] ?? '';
                $this->emergency_contact_1_relationship = $progress['emergency_contacts']['contact_1']['relationship'] ?? '';

                $this->emergency_contact_2_name = $progress['emergency_contacts']['contact_2']['name'] ?? '';
                $this->emergency_contact_2_phone = $progress['emergency_contacts']['contact_2']['phone'] ?? '';
                $this->emergency_contact_2_relationship = $progress['emergency_contacts']['contact_2']['relationship'] ?? '';
            }

            // 恢復步驟狀態
            if (isset($progress['step_completed'])) {
                $this->step1Completed = $progress['step_completed']['step1'] ?? false;
                $this->step2Completed = $progress['step_completed']['step2'] ?? false;
                $this->step3Completed = $progress['step_completed']['step3'] ?? false;
                $this->step4Completed = $progress['step_completed']['step4'] ?? false;
                $this->step5Completed = $progress['step_completed']['step5'] ?? false;
            }

            // 恢復當前步驟
            $this->currentStep = $progress['current_step'] ?? 1;
        }
    }

    /**
     * 檢查檔案是否存在臨時預覽
     */
    public function hasTemporaryFile($propertyName)
    {
        return property_exists($this, $propertyName) &&
               $this->$propertyName &&
               method_exists($this->$propertyName, 'temporaryUrl');
    }

    /**
     * 獲取檔案大小（格式化）
     */
    public function getFileSize($propertyName)
    {
        if (!$this->hasTemporaryFile($propertyName)) {
            return null;
        }

        $bytes = $this->$propertyName->getSize();

        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * 獲取檔案MIME類型
     */
    public function getFileMimeType($propertyName)
    {
        if (!$this->hasTemporaryFile($propertyName)) {
            return null;
        }

        return $this->$propertyName->getMimeType();
    }

    /**
     * 驗證檔案是否為圖片
     */
    public function isImageFile($propertyName)
    {
        if (!$this->hasTemporaryFile($propertyName)) {
            return false;
        }

        $mimeType = $this->getFileMimeType($propertyName);
        return strpos($mimeType, 'image/') === 0;
    }

    /**
     * 即時驗證
     */
    public function updated($propertyName)
    {
        // 只驗證當前步驟的欄位
        if ($this->shouldValidateProperty($propertyName)) {
            try {
                $this->validateOnly($propertyName);
            } catch (\Illuminate\Validation\ValidationException $e) {
                // 驗證失敗時不需要特別處理，Livewire 會自動顯示錯誤
            }
        }

        // 檔案上傳完成時的處理
        if (in_array($propertyName, ['id_card_front', 'id_card_back', 'id_card_selfie', 'second_document', 'bank_card'])) {
            if ($this->$propertyName) {
                $this->dispatch('file-selected', property: $propertyName);
            }
        }

        // 每次更新都保存到 session
        $this->saveProgressToSession();
    }

    /**
     * 判斷是否應該驗證該屬性
     */
    private function shouldValidateProperty($propertyName)
    {
        $step1Properties = ['name', 'phone', 'occupation', 'city', 'address', 'contact_time', 'line_id', 'amount'];
        $step2Properties = ['emergency_contact_1_name', 'emergency_contact_1_phone', 'emergency_contact_1_relationship',
                           'emergency_contact_2_name', 'emergency_contact_2_phone', 'emergency_contact_2_relationship'];
        $step3Properties = ['id_card_front', 'id_card_back', 'id_card_selfie', 'second_document'];
        $step4Properties = ['bank_card'];

        switch ($this->currentStep) {
            case 1:
                return in_array($propertyName, $step1Properties);
            case 2:
                return in_array($propertyName, $step2Properties);
            case 3:
                return in_array($propertyName, $step3Properties);
            case 4:
                return in_array($propertyName, $step4Properties);
            default:
                return false;
        }
    }

    /**
     * 格式化手機號碼輸入
     */
    public function updatedPhone($value)
    {
        $this->phone = preg_replace('/[^0-9]/', '', $value);
        if (strlen($this->phone) > 10) {
            $this->phone = substr($this->phone, 0, 10);
        }
    }

    public function updatedEmergencyContact1Phone($value)
    {
        $this->emergency_contact_1_phone = preg_replace('/[^0-9]/', '', $value);
        if (strlen($this->emergency_contact_1_phone) > 10) {
            $this->emergency_contact_1_phone = substr($this->emergency_contact_1_phone, 0, 10);
        }
    }

    public function updatedEmergencyContact2Phone($value)
    {
        $this->emergency_contact_2_phone = preg_replace('/[^0-9]/', '', $value);
        if (strlen($this->emergency_contact_2_phone) > 10) {
            $this->emergency_contact_2_phone = substr($this->emergency_contact_2_phone, 0, 10);
        }
    }

    /**
     * 重置表單
     */
    public function resetForm()
    {
        $this->reset(['name', 'phone', 'occupation', 'city', 'address', 'contact_time', 'line_id',
                     'emergency_contact_1_name', 'emergency_contact_1_phone', 'emergency_contact_1_relationship',
                     'emergency_contact_2_name', 'emergency_contact_2_phone', 'emergency_contact_2_relationship',
                     'id_card_front', 'id_card_back', 'id_card_selfie', 'second_document', 'bank_card']);

        $this->currentStep = 1;
        $this->step1Completed = false;
        $this->step2Completed = false;
        $this->step3Completed = false;
        $this->step4Completed = false;
        $this->step5Completed = false;
        $this->applicationId = null;

        // 清理 session
        $this->clearSessionData();
    }

    /**
     * 清理 session 資料
     */
    private function clearSessionData()
    {
        session()->forget([
            'loan_application_progress',
            'loan_amount'
        ]);
    }

    /**
     * 檢查當前步驟是否完成
     */
    public function isStepCompleted($step)
    {
        $completedProperty = "step{$step}Completed";
        return property_exists($this, $completedProperty) && $this->$completedProperty;
    }

    /**
     * 獲取當前步驟的完成度百分比
     */
    public function getProgressPercentage()
    {
        return ($this->currentStep / 5) * 100;
    }

    /**
     * 檢查是否可以進行下一步
     */
    public function canProceedToNextStep()
    {
        switch ($this->currentStep) {
            case 1:
                return !empty($this->name) &&
                       !empty($this->phone) &&
                       !empty($this->occupation) &&
                       !empty($this->city) &&
                       !empty($this->address) &&
                       !empty($this->contact_time);

            case 2:
                return !empty($this->emergency_contact_1_name) &&
                       !empty($this->emergency_contact_1_phone) &&
                       !empty($this->emergency_contact_1_relationship) &&
                       !empty($this->emergency_contact_2_name) &&
                       !empty($this->emergency_contact_2_phone) &&
                       !empty($this->emergency_contact_2_relationship);

            case 3:
                return $this->id_card_front &&
                       $this->id_card_back &&
                       $this->id_card_selfie &&
                       $this->second_document;

            case 4:
                return true; // 第4步是選填的

            default:
                return false;
        }
    }

    /**
     * 獲取步驟標題
     */
    public function getStepTitle($step = null)
    {
        $step = $step ?? $this->currentStep;

        $titles = [
            1 => '基本資料',
            2 => '緊急聯絡人',
            3 => '證件上傳',
            4 => '銀行資訊',
            5 => '申請完成'
        ];

        return $titles[$step] ?? '未知步驟';
    }

    /**
     * 獲取步驟描述
     */
    public function getStepDescription($step = null)
    {
        $step = $step ?? $this->currentStep;

        $descriptions = [
            1 => '請填寫您的基本個人資料',
            2 => '請填寫兩位緊急聯絡人，關係限父母或兄弟姊妹',
            3 => '請上傳清晰的證件照片，確保資訊完整可見',
            4 => '此步驟為選填，可直接跳過或上傳銀行卡/存摺正面',
            5 => '恭喜您完成申請！'
        ];

        return $descriptions[$step] ?? '';
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
     * 獲取關係選項
     */
    public function getRelationshipOptions()
    {
        return ['父親', '母親', '兄弟', '姊妹'];
    }

    /**
     * 獲取申請摘要（用於最後確認頁面）
     */
    public function getApplicationSummary()
    {
        return [
            'basic_info' => [
                'name' => $this->name,
                'phone' => $this->phone,
                'occupation' => $this->occupation,
                'city' => $this->city,
                'address' => $this->address,
                'contact_time' => $this->contact_time,
                'line_id' => $this->line_id,
                'amount' => $this->amount,
            ],
            'emergency_contacts' => [
                [
                    'name' => $this->emergency_contact_1_name,
                    'phone' => $this->emergency_contact_1_phone,
                    'relationship' => $this->emergency_contact_1_relationship,
                ],
                [
                    'name' => $this->emergency_contact_2_name,
                    'phone' => $this->emergency_contact_2_phone,
                    'relationship' => $this->emergency_contact_2_relationship,
                ]
            ],
            'documents_uploaded' => [
                'id_card_front' => !empty($this->id_card_front),
                'id_card_back' => !empty($this->id_card_back),
                'id_card_selfie' => !empty($this->id_card_selfie),
                'second_document' => !empty($this->second_document),
                'bank_card' => !empty($this->bank_card),
            ]
        ];
    }

    public function render()
    {
        return view('livewire.input-data-component', [
            'cityOptions' => $this->getCityOptions(),
            'contactTimeOptions' => $this->getContactTimeOptions(),
            'relationshipOptions' => $this->getRelationshipOptions(),
            'progressPercentage' => $this->getProgressPercentage(),
            'canProceed' => $this->canProceedToNextStep(),
            'stepTitle' => $this->getStepTitle(),
            'stepDescription' => $this->getStepDescription(),
            'applicationSummary' => $this->currentStep >= 5 ? $this->getApplicationSummary() : null,
            'amountLimits' => $this->getLoanAmountLimits(), // 添加這一行
        ])->layout('layouts.app', ['title' => '貸款申請 - ' . $this->getStepTitle()]);
    }
}
