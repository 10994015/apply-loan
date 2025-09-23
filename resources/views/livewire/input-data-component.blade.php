<div class="loan-register-container">
    <!-- Header -->
    <div class="header">
        <button class="back-btn" onclick="history.back()">
            <i class="fas fa-chevron-left"></i>
        </button>
        <h1 class="header-title">登錄</h1>
    </div>

    <!-- Logo Section -->
    <div class="logo-section">
        <div class="logo">
            <div class="logo-icon">
                <i class="fas fa-user-circle"></i>
            </div>
        </div>
    </div>

    <!-- Welcome Text -->
    <div class="welcome-section">
        <h2 class="welcome-title">歡迎使用登您來</h2>
        <p class="welcome-subtitle">5分鐘內即可完成申請 10分鐘內獲得貸款</p>
    </div>

    <!-- Form Section -->
    <div class="form-section">
        <form wire:submit="submitApplication">
            <!-- Name Input -->
            <div class="form-group">
                <label class="form-label">姓名 <span class="required">*</span></label>
                <input
                    type="text"
                    wire:model="name"
                    class="form-input @error('name') error @enderror"
                    placeholder="請輸入您的真實姓名"
                    maxlength="50"
                >
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Phone Number Input -->
            <div class="form-group">
                <label class="form-label">電話 <span class="required">*</span></label>
                <div class="phone-input-group">
                    <div class="country-code">+886</div>
                    <input
                        type="tel"
                        wire:model="phone"
                        class="phone-input @error('phone') error @enderror"
                        placeholder="請輸入您的電話號碼"
                        maxlength="10"
                    >
                </div>
                @error('phone')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Occupation Input -->
            <div class="form-group">
                <label class="form-label">職業 <span class="required">*</span></label>
                <input
                    type="text"
                    wire:model="occupation"
                    class="form-input @error('occupation') error @enderror"
                    placeholder="請輸入您的職業"
                    maxlength="100"
                >
                @error('occupation')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- City Select -->
            <div class="form-group">
                <label class="form-label">居住縣市 <span class="required">*</span></label>
                <select
                    wire:model="city"
                    class="form-select @error('city') error @enderror"
                >
                    <option value="">請選擇縣市</option>
                    <option value="台北市">台北市</option>
                    <option value="新北市">新北市</option>
                    <option value="桃園市">桃園市</option>
                    <option value="台中市">台中市</option>
                    <option value="台南市">台南市</option>
                    <option value="高雄市">高雄市</option>
                    <option value="基隆市">基隆市</option>
                    <option value="新竹市">新竹市</option>
                    <option value="嘉義市">嘉義市</option>
                    <option value="新竹縣">新竹縣</option>
                    <option value="苗栗縣">苗栗縣</option>
                    <option value="彰化縣">彰化縣</option>
                    <option value="南投縣">南投縣</option>
                    <option value="雲林縣">雲林縣</option>
                    <option value="嘉義縣">嘉義縣</option>
                    <option value="屏東縣">屏東縣</option>
                    <option value="宜蘭縣">宜蘭縣</option>
                    <option value="花蓮縣">花蓮縣</option>
                    <option value="台東縣">台東縣</option>
                    <option value="澎湖縣">澎湖縣</option>
                    <option value="金門縣">金門縣</option>
                    <option value="連江縣">連江縣</option>
                </select>
                @error('city')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Contact Time Input -->
            <div class="form-group">
                <label class="form-label">方便聯繫時間 <span class="required">*</span></label>
                <select
                    wire:model="contact_time"
                    class="form-select @error('contact_time') error @enderror"
                >
                    <option value="">請選擇方便聯繫的時間</option>
                    <option value="上午 09:00-12:00">上午 09:00-12:00</option>
                    <option value="下午 12:00-18:00">下午 12:00-18:00</option>
                    <option value="晚上 18:00-21:00">晚上 18:00-21:00</option>
                    <option value="平日任何時間">平日任何時間</option>
                    <option value="週末任何時間">週末任何時間</option>
                    <option value="隨時">隨時</option>
                </select>
                @error('contact_time')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Line ID Input (Optional) -->
            <div class="form-group">
                <label class="form-label">Line ID <span class="optional">(選填)</span></label>
                <input
                    type="text"
                    wire:model="line_id"
                    class="form-input @error('line_id') error @enderror"
                    placeholder="請輸入您的 Line ID (選填)"
                    maxlength="50"
                >
                @error('line_id')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Amount Display -->
            <div class="amount-section">
                <label class="amount-label">申請金額</label>
                <div class="amount-display">
                    ${{ number_format($amount, 0) }}
                </div>
                <input type="hidden" wire:model="amount">
            </div>

            <!-- Important Notice -->
            <div class="notice-section">
                <p class="notice-text">
                    請注意：請使用有效電話號碼，無效電話號碼會導致申請失敗
                    如未收到簡訊，請聯繫客服Line：@390raqwz
                </p>
            </div>

            <!-- Submit Button -->
            <div class="submit-section">
                <button
                    type="submit"
                    class="submit-btn"
                    wire:loading.attr="disabled"
                    wire:target="submitApplication"
                >
                    <span wire:loading.remove wire:target="submitApplication">登錄</span>
                    <span wire:loading wire:target="submitApplication">
                        <i class="fas fa-spinner fa-spin"></i> 處理中...
                    </span>
                </button>
            </div>
        </form>

        <!-- Terms -->
        <div class="terms-section">
            <p class="terms-text">
                借款前請詳細作個人信用與還款能力，切勿在會發繳、金融、無能力下使用
                本程式
            </p>
        </div>
    </div>

    <!-- Success Message -->
    @if (session()->has('success'))
        <div class="success-message">
            <div class="success-content">
                <i class="fas fa-check-circle"></i>
                <h3>申請成功！</h3>
                <p>{{ session('success') }}</p>
                <button onclick="window.location.href='{{ route('loan.index') }}'" class="back-home-btn">
                    返回首頁
                </button>
            </div>
        </div>
    @endif


<style>
.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #333;
    font-size: 14px;
}

.required {
    color: #e74c3c;
}

.optional {
    color: #7f8c8d;
    font-size: 12px;
}

.form-input, .form-select {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 16px;
    transition: all 0.3s ease;
    background: white;
}

.form-input:focus, .form-select:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.1);
}

.form-input.error, .form-select.error {
    border-color: #e74c3c;
    box-shadow: 0 0 0 2px rgba(231, 76, 60, 0.1);
}

.error-message {
    display: block;
    margin-top: 5px;
    color: #e74c3c;
    font-size: 12px;
}

.phone-input-group {
    display: flex;
    align-items: center;
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.phone-input-group:focus-within {
    border-color: #3498db;
    box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.1);
}

.phone-input-group.error {
    border-color: #e74c3c;
}

.country-code {
    background: #f8f9fa;
    padding: 12px 16px;
    border-right: 1px solid #ddd;
    font-weight: 500;
    color: #666;
}

.phone-input {
    border: none;
    flex: 1;
    padding: 12px 16px;
    font-size: 16px;
}

.phone-input:focus {
    outline: none;
    box-shadow: none;
    border: none;
}

.amount-section {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 20px;
    border: 2px solid #e9ecef;
}

.amount-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #333;
    font-size: 14px;
}

.amount-display {
    font-size: 32px;
    font-weight: bold;
    color: #2c3e50;
    text-align: center;
}

.notice-section {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 20px;
}

.notice-text {
    margin: 0;
    font-size: 13px;
    color: #856404;
    line-height: 1.4;
}

.submit-section {
    margin-top: 30px;
}

.submit-btn {
    width: 100%;
    padding: 16px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 18px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.submit-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.submit-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}

.terms-section {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #eee;
}

.terms-text {
    font-size: 12px;
    color: #666;
    line-height: 1.4;
    text-align: center;
    margin: 0;
}
    </style>
</div>
