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
        <h2 class="welcome-title">歡迎使用人人借</h2>
        <p class="welcome-subtitle">5分鐘內即可完成申請 10分鐘內獲得貸款</p>
    </div>

    <!-- Form Section -->
    <div class="form-section">
        <form wire:submit="submitApplication">
            <!-- Phone Number Input -->
            <div class="form-group">
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

            <!-- Verification Code Input (Hidden since we don't need SMS) -->
            <div class="form-group" style="display: none;">
                <div class="verification-group">
                    <input
                        type="text"
                        class="verification-input"
                        placeholder="請輸入簡訊驗證碼"
                        readonly
                    >
                    <button type="button" class="send-code-btn" disabled>
                        發送簡訊
                    </button>
                </div>
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
</div>

