<div class="loan-register-container">
    <!-- Header -->
    <div class="header">
        <button class="back-btn" onclick="history.back()">
            <i class="fas fa-chevron-left"></i>
        </button>
        <h1 class="header-title">貸款申請</h1>
    </div>

    <!-- Progress Bar -->
    <div class="progress-container">
        <div class="progress-bar">
            <div class="progress-fill" style="width: {{ ($currentStep / 5) * 100 }}%"></div>
        </div>
        <div class="progress-steps">
            <div class="step {{ $currentStep >= 1 ? 'active' : '' }} {{ $step1Completed ? 'completed' : '' }}">
                <div class="step-number">1</div>
                <div class="step-label">基本資料</div>
            </div>
            <div class="step {{ $currentStep >= 2 ? 'active' : '' }} {{ $step2Completed ? 'completed' : '' }}">
                <div class="step-number">2</div>
                <div class="step-label">緊急聯絡人</div>
            </div>
            <div class="step {{ $currentStep >= 3 ? 'active' : '' }} {{ $step3Completed ? 'completed' : '' }}">
                <div class="step-number">3</div>
                <div class="step-label">證件上傳</div>
            </div>
            <div class="step {{ $currentStep >= 4 ? 'active' : '' }} {{ $step4Completed ? 'completed' : '' }}">
                <div class="step-number">4</div>
                <div class="step-label">銀行資訊</div>
            </div>
            <div class="step {{ $currentStep >= 5 ? 'active' : '' }} {{ $step5Completed ? 'completed' : '' }}">
                <div class="step-number">5</div>
                <div class="step-label">申請完成</div>
            </div>
        </div>
    </div>

    <!-- Logo Section (只在第一步顯示) -->
    @if($currentStep == 1)
    <div class="logo-section">
        <div class="logo">
            <div class="logo-icon">
                <i class="fas fa-user-circle"></i>
            </div>
        </div>
        <div class="welcome-section">
            <h2 class="welcome-title">歡迎使用登您來</h2>
            <p class="welcome-subtitle">5分鐘內即可完成申請 10分鐘內獲得貸款</p>
        </div>
    </div>
    @endif

    <!-- Form Section -->
    <div class="form-section">
        <form wire:submit="nextStep">

            <!-- Step 1: 基本資料 -->
            @if($currentStep == 1)
            <div class="step-content">
                <h3 class="step-title">填寫基本資料</h3>

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

                <!-- Address Input -->
                <div class="form-group">
                    <label class="form-label">詳細地址 <span class="required">*</span></label>
                    <input
                        type="text"
                        wire:model="address"
                        class="form-input @error('address') error @enderror"
                        placeholder="請輸入詳細地址（區/鄉鎮市、路/街、巷弄、號碼等）"
                        maxlength="200"
                    >
                    <div class="form-hint">請輸入完整地址，包含區域、街道、巷弄及門牌號碼</div>
                    @error('address')
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

                <!-- Line ID Input -->
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
            </div>
            @endif

            <!-- Step 2: 緊急聯絡人 -->
            @if($currentStep == 2)
            <div class="step-content">
                <h3 class="step-title">填寫緊急聯絡人資料</h3>
                <p class="step-description">請填寫兩位緊急聯絡人，關係限父母或兄弟姊妹</p>

                <!-- Emergency Contact 1 -->
                <div class="contact-group">
                    <h4 class="contact-title">緊急聯絡人 1</h4>

                    <div class="form-group">
                        <label class="form-label">姓名 <span class="required">*</span></label>
                        <input
                            type="text"
                            wire:model="emergency_contact_1_name"
                            class="form-input @error('emergency_contact_1_name') error @enderror"
                            placeholder="請輸入緊急聯絡人姓名"
                            maxlength="50"
                        >
                        @error('emergency_contact_1_name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">手機號碼 <span class="required">*</span></label>
                        <div class="phone-input-group">
                            <div class="country-code">+886</div>
                            <input
                                type="tel"
                                wire:model="emergency_contact_1_phone"
                                class="phone-input @error('emergency_contact_1_phone') error @enderror"
                                placeholder="請輸入手機號碼"
                                maxlength="10"
                            >
                        </div>
                        @error('emergency_contact_1_phone')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">關係 <span class="required">*</span></label>
                        <select
                            wire:model="emergency_contact_1_relationship"
                            class="form-select @error('emergency_contact_1_relationship') error @enderror"
                        >
                            <option value="">請選擇關係</option>
                            <option value="父親">父親</option>
                            <option value="母親">母親</option>
                            <option value="兄弟">兄弟</option>
                            <option value="姊妹">姊妹</option>
                        </select>
                        @error('emergency_contact_1_relationship')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Emergency Contact 2 -->
                <div class="contact-group">
                    <h4 class="contact-title">緊急聯絡人 2</h4>

                    <div class="form-group">
                        <label class="form-label">姓名 <span class="required">*</span></label>
                        <input
                            type="text"
                            wire:model="emergency_contact_2_name"
                            class="form-input @error('emergency_contact_2_name') error @enderror"
                            placeholder="請輸入緊急聯絡人姓名"
                            maxlength="50"
                        >
                        @error('emergency_contact_2_name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">手機號碼 <span class="required">*</span></label>
                        <div class="phone-input-group">
                            <div class="country-code">+886</div>
                            <input
                                type="tel"
                                wire:model="emergency_contact_2_phone"
                                class="phone-input @error('emergency_contact_2_phone') error @enderror"
                                placeholder="請輸入手機號碼"
                                maxlength="10"
                            >
                        </div>
                        @error('emergency_contact_2_phone')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">關係 <span class="required">*</span></label>
                        <select
                            wire:model="emergency_contact_2_relationship"
                            class="form-select @error('emergency_contact_2_relationship') error @enderror"
                        >
                            <option value="">請選擇關係</option>
                            <option value="父親">父親</option>
                            <option value="母親">母親</option>
                            <option value="兄弟">兄弟</option>
                            <option value="姊妹">姊妹</option>
                        </select>
                        @error('emergency_contact_2_relationship')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            @endif

            <!-- Step 3: 證件上傳 (改進版本，加入圖片預覽) -->
            @if($currentStep == 3)
            <div class="step-content">
                <h3 class="step-title">上傳身分證件</h3>
                <p class="step-description">請上傳清晰的證件照片，確保資訊完整可見</p>

                <!-- ID Card Front -->
                <div class="upload-group">
                    <label class="upload-label">身分證正面 <span class="required">*</span></label>

                    <!-- Image Preview -->
                    @if($id_card_front)
                        <div class="image-preview-container">
                            <div class="image-preview">
                                <img src="{{ $id_card_front->temporaryUrl() }}" alt="身分證正面預覽" class="preview-image">
                                <div class="preview-overlay">
                                    <button type="button" wire:click="removeFile('id_card_front')" class="remove-image-btn">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="image-info">
                                <i class="fas fa-check-circle text-success"></i>
                                <span class="file-name">{{ $id_card_front->getClientOriginalName() }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Upload Area -->
                    <div class="upload-area @error('id_card_front') error @enderror @if($id_card_front) has-file @endif">
                        <input type="file" wire:model="id_card_front" accept="image/*" class="upload-input" id="id_card_front">
                        <label for="id_card_front" class="upload-button">
                            @if($id_card_front)
                                <i class="fas fa-sync-alt"></i>
                                <span>重新選擇檔案</span>
                            @else
                                <i class="fas fa-camera"></i>
                                <span>點擊上傳身分證正面</span>
                            @endif
                        </label>

                        <!-- Loading State -->
                        <div wire:loading wire:target="id_card_front" class="upload-loading">
                            <i class="fas fa-spinner fa-spin"></i>
                            <span>上傳中...</span>
                        </div>
                    </div>

                    @error('id_card_front')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- ID Card Back -->
                <div class="upload-group">
                    <label class="upload-label">身分證反面 <span class="required">*</span></label>

                    <!-- Image Preview -->
                    @if($id_card_back)
                        <div class="image-preview-container">
                            <div class="image-preview">
                                <img src="{{ $id_card_back->temporaryUrl() }}" alt="身分證反面預覽" class="preview-image">
                                <div class="preview-overlay">
                                    <button type="button" wire:click="removeFile('id_card_back')" class="remove-image-btn">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="image-info">
                                <i class="fas fa-check-circle text-success"></i>
                                <span class="file-name">{{ $id_card_back->getClientOriginalName() }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Upload Area -->
                    <div class="upload-area @error('id_card_back') error @enderror @if($id_card_back) has-file @endif">
                        <input type="file" wire:model="id_card_back" accept="image/*" class="upload-input" id="id_card_back">
                        <label for="id_card_back" class="upload-button">
                            @if($id_card_back)
                                <i class="fas fa-sync-alt"></i>
                                <span>重新選擇檔案</span>
                            @else
                                <i class="fas fa-camera"></i>
                                <span>點擊上傳身分證反面</span>
                            @endif
                        </label>

                        <!-- Loading State -->
                        <div wire:loading wire:target="id_card_back" class="upload-loading">
                            <i class="fas fa-spinner fa-spin"></i>
                            <span>上傳中...</span>
                        </div>
                    </div>

                    @error('id_card_back')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- ID Card Selfie -->
                <div class="upload-group">
                    <label class="upload-label">手持身分證自拍 <span class="required">*</span></label>

                    <!-- Image Preview -->
                    @if($id_card_selfie)
                        <div class="image-preview-container">
                            <div class="image-preview">
                                <img src="{{ $id_card_selfie->temporaryUrl() }}" alt="手持身分證自拍預覽" class="preview-image">
                                <div class="preview-overlay">
                                    <button type="button" wire:click="removeFile('id_card_selfie')" class="remove-image-btn">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="image-info">
                                <i class="fas fa-check-circle text-success"></i>
                                <span class="file-name">{{ $id_card_selfie->getClientOriginalName() }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Upload Area -->
                    <div class="upload-area @error('id_card_selfie') error @enderror @if($id_card_selfie) has-file @endif">
                        <input type="file" wire:model="id_card_selfie" accept="image/*" class="upload-input" id="id_card_selfie">
                        <label for="id_card_selfie" class="upload-button">
                            @if($id_card_selfie)
                                <i class="fas fa-sync-alt"></i>
                                <span>重新選擇檔案</span>
                            @else
                                <i class="fas fa-camera"></i>
                                <span>點擊上傳手持身分證自拍</span>
                            @endif
                        </label>

                        <!-- Loading State -->
                        <div wire:loading wire:target="id_card_selfie" class="upload-loading">
                            <i class="fas fa-spinner fa-spin"></i>
                            <span>上傳中...</span>
                        </div>
                    </div>

                    <div class="upload-hint">請手持身分證進行自拍，確保人臉和證件清晰可見</div>
                    @error('id_card_selfie')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Second Document -->
                <div class="upload-group">
                    <label class="upload-label">第二證件 <span class="required">*</span></label>

                    <!-- Image Preview -->
                    @if($second_document)
                        <div class="image-preview-container">
                            <div class="image-preview">
                                <img src="{{ $second_document->temporaryUrl() }}" alt="第二證件預覽" class="preview-image">
                                <div class="preview-overlay">
                                    <button type="button" wire:click="removeFile('second_document')" class="remove-image-btn">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="image-info">
                                <i class="fas fa-check-circle text-success"></i>
                                <span class="file-name">{{ $second_document->getClientOriginalName() }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Upload Area -->
                    <div class="upload-area @error('second_document') error @enderror @if($second_document) has-file @endif">
                        <input type="file" wire:model="second_document" accept="image/*" class="upload-input" id="second_document">
                        <label for="second_document" class="upload-button">
                            @if($second_document)
                                <i class="fas fa-sync-alt"></i>
                                <span>重新選擇檔案</span>
                            @else
                                <i class="fas fa-camera"></i>
                                <span>點擊上傳第二證件</span>
                            @endif
                        </label>

                        <!-- Loading State -->
                        <div wire:loading wire:target="second_document" class="upload-loading">
                            <i class="fas fa-spinner fa-spin"></i>
                            <span>上傳中...</span>
                        </div>
                    </div>

                    <div class="upload-hint">可上傳駕照、健保卡或護照等證件</div>
                    @error('second_document')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            @endif

            <!-- Step 4: 銀行資訊上傳 -->
            @if($currentStep == 4)
            <div class="step-content">
                <h3 class="step-title">上傳銀行資訊</h3>
                <p class="step-description">此步驟為選填，可直接跳過或上傳銀行卡/存摺正面</p>

                <!-- Bank Card Upload -->
                <div class="upload-group">
                    <label class="upload-label">銀行卡或存摺正面 <span class="optional">(選填)</span></label>

                    <!-- Image Preview -->
                    @if($bank_card)
                        <div class="image-preview-container">
                            <div class="image-preview">
                                <img src="{{ $bank_card->temporaryUrl() }}" alt="銀行卡預覽" class="preview-image">
                                <div class="preview-overlay">
                                    <button type="button" wire:click="removeFile('bank_card')" class="remove-image-btn">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="image-info">
                                <i class="fas fa-check-circle text-success"></i>
                                <span class="file-name">{{ $bank_card->getClientOriginalName() }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Upload Area -->
                    <div class="upload-area @error('bank_card') error @enderror @if($bank_card) has-file @endif">
                        <input type="file" wire:model="bank_card" accept="image/*" class="upload-input" id="bank_card">
                        <label for="bank_card" class="upload-button">
                            @if($bank_card)
                                <i class="fas fa-sync-alt"></i>
                                <span>重新選擇檔案</span>
                            @else
                                <i class="fas fa-credit-card"></i>
                                <span>點擊上傳銀行卡或存摺正面</span>
                            @endif
                        </label>

                        <!-- Loading State -->
                        <div wire:loading wire:target="bank_card" class="upload-loading">
                            <i class="fas fa-spinner fa-spin"></i>
                            <span>上傳中...</span>
                        </div>
                    </div>

                    <div class="upload-hint">上傳銀行卡或存摺正面，有助於加速審核流程</div>
                    @error('bank_card')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="skip-section">
                    <button type="button" wire:click="skipStep" class="skip-btn">
                        跳過此步驟
                    </button>
                </div>
            </div>
            @endif

            <!-- Step 5: 成功頁面 -->
            @if($currentStep == 5)
            <div class="step-content success-content">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3 class="success-title">申請提交成功！</h3>
                <div class="success-info">
                    <p><strong>申請人：</strong>{{ $name }}</p>
                    <p><strong>申請金額：</strong>${{ number_format($amount, 0) }}</p>
                    <p><strong>申請編號：</strong>{{ str_pad($applicationId ?? 0, 6, '0', STR_PAD_LEFT) }}</p>
                </div>
                <div class="success-message">
                    <p>我們會在1個工作日內聯繫您，請保持電話暢通。</p>
                    <p>如有任何問題，請聯繫客服Line：
                        <a href="https://line.me/ti/p/sxmWNW-UtV">@luck1688luck</a>
                    </p>
                </div>
                <button type="button" onclick="window.location.href='{{ route('loan.index') }}'" class="back-home-btn">
                    返回首頁
                </button>

                <!-- 新增：開始新申請按鈕 -->
                <button type="button" wire:click="startNewApplication" class="new-application-btn">
                    開始新申請
                </button>
            </div>
            @endif

            <!-- Navigation Buttons -->
            @if($currentStep < 5)
            <div class="navigation-section">
                @if($currentStep > 1)
                <button type="button" wire:click="previousStep" class="prev-btn">
                    <i class="fas fa-chevron-left"></i> 上一步
                </button>
                @endif

                <button type="submit" class="next-btn" wire:loading.attr="disabled" wire:target="nextStep">
                    <span wire:loading.remove wire:target="nextStep">
                        @if($currentStep == 4)
                            完成申請
                        @else
                            下一步 <i class="fas fa-chevron-right"></i>
                        @endif
                    </span>
                    <span wire:loading wire:target="nextStep">
                        <i class="fas fa-spinner fa-spin"></i> 處理中...
                    </span>
                </button>
            </div>
            @endif

            <!-- Important Notice -->
            @if($currentStep == 1)
            <div class="notice-section">
                <p class="notice-text">
                    請注意：請使用有效電話號碼，無效電話號碼會導致申請失敗
                    如未收到簡訊，請聯繫客服Line：
                    <a href="https://line.me/ti/p/sxmWNW-UtV">
                        @luck1688luck
                    </a>
                </p>
            </div>
            @endif
        </form>

        <!-- Terms -->
        @if($currentStep == 1)
        <div class="terms-section">
            <p class="terms-text">
                借款前請詳細作個人信用與還款能力，切勿在會發繳、金融、無能力下使用本程式
            </p>
        </div>
        @endif
    </div>

    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

/* 基本樣式 */
.loan-register-container {
    max-width: 480px;
    margin: 0 auto;
    background: #fff;
    min-height: 100vh;
    font-family: 'Noto Sans TC', sans-serif;
}

.header {
    display: flex;
    align-items: center;
    padding: 16px 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    position: sticky;
    top: 0;
    z-index: 100;
}

.back-btn {
    background: none;
    border: none;
    color: white;
    font-size: 20px;
    cursor: pointer;
    margin-right: 16px;
}

.header-title {
    font-size: 18px;
    font-weight: 600;
    margin: 0;
}

/* Progress Bar */
.progress-container {
    padding: 20px;
    background: #f8f9fa;
}

.progress-bar {
    height: 4px;
    background: #e9ecef;
    border-radius: 2px;
    margin-bottom: 16px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    transition: width 0.3s ease;
}

.progress-steps {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1;
}

.step-number {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #e9ecef;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 4px;
    transition: all 0.3s ease;
}

.step.active .step-number {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.step.completed .step-number {
    background: #28a745;
    color: white;
}

.step-label {
    font-size: 11px;
    color: #6c757d;
    text-align: center;
}

.step.active .step-label {
    color: #495057;
    font-weight: 500;
}

/* Logo Section */
.logo-section {
    text-align: center;
    padding: 30px 20px 20px;
}

.logo-icon {
    font-size: 64px;
    color: #667eea;
    margin-bottom: 16px;
}

.welcome-title {
    font-size: 24px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 8px;
}

.welcome-subtitle {
    font-size: 14px;
    color: #7f8c8d;
    margin: 0;
}

/* Form Styles */
.form-section {
    padding: 0 20px 20px;
}

.step-content {
    padding: 20px 0;
}

.step-title {
    font-size: 20px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 8px;
}

.step-description {
    color: #6c757d;
    font-size: 14px;
    margin-bottom: 24px;
}

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
    box-sizing: border-box;
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

/* Contact Groups */
.contact-group {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    border: 1px solid #e9ecef;
}

.contact-title {
    font-size: 16px;
    font-weight: 600;
    color: #495057;
    margin-bottom: 16px;
    border-bottom: 2px solid #dee2e6;
    padding-bottom: 8px;
}

/* 改進的上傳樣式 - 支援圖片預覽 */
.upload-group {
    margin-bottom: 24px;
}

.upload-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #333;
    font-size: 14px;
}

/* 圖片預覽容器 */
.image-preview-container {
    margin-bottom: 12px;
    border: 2px solid #28a745;
    border-radius: 12px;
    overflow: hidden;
    background: #f8fff9;
}

.image-preview {
    position: relative;
    width: 100%;
    max-height: 200px;
    overflow: hidden;
}

.preview-image {
    width: 100%;
    height: auto;
    max-height: 200px;
    object-fit: cover;
    display: block;
}

.preview-overlay {
    position: absolute;
    top: 8px;
    right: 8px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.image-preview:hover .preview-overlay {
    opacity: 1;
}

.remove-image-btn {
    background: rgba(231, 76, 60, 0.9);
    color: white;
    border: none;
    border-radius: 50%;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
}

.remove-image-btn:hover {
    background: rgba(231, 76, 60, 1);
    transform: scale(1.1);
}

.image-info {
    padding: 12px 16px;
    display: flex;
    align-items: center;
    gap: 8px;
    background: rgba(40, 167, 69, 0.1);
    border-top: 1px solid rgba(40, 167, 69, 0.2);
}

.text-success {
    color: #28a745;
}

.file-name {
    font-size: 14px;
    color: #495057;
    font-weight: 500;
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* 上傳區域 */
.upload-area {
    border: 2px dashed #dee2e6;
    border-radius: 12px;
    padding: 0;
    text-align: center;
    transition: all 0.3s ease;
    overflow: hidden;
    position: relative;
}

.upload-area:hover {
    border-color: #3498db;
    background: #f8f9fa;
}

.upload-area.error {
    border-color: #e74c3c;
    background: #fdf2f2;
}

.upload-area.has-file {
    border-color: #28a745;
    background: #f8fff9;
}

.upload-input {
    display: none;
}

.upload-button {
    display: block;
    padding: 24px 16px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
    color: #6c757d;
    border: none;
    width: 100%;
}

.upload-button:hover {
    background: #f8f9fa;
    color: #495057;
}

.upload-area.has-file .upload-button {
    color: #28a745;
    padding: 16px;
}

.upload-button i {
    font-size: 24px;
    margin-bottom: 8px;
    display: block;
}

.upload-area.has-file .upload-button i {
    font-size: 18px;
    margin-bottom: 4px;
}

.upload-button span {
    font-weight: 500;
    font-size: 14px;
}

/* 上傳載入狀態 */
.upload-loading {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.95);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    color: #667eea;
    font-weight: 500;
}

.upload-loading i {
    font-size: 24px;
}

.upload-hint {
    font-size: 12px;
    color: #6c757d;
    margin-top: 8px;
    text-align: center;
}

.skip-section {
    text-align: center;
    margin-top: 24px;
    padding-top: 24px;
    border-top: 1px solid #dee2e6;
}

.skip-btn {
    background: none;
    border: 2px solid #6c757d;
    color: #6c757d;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.skip-btn:hover {
    background: #6c757d;
    color: white;
}

/* Success Page */
.success-content {
    text-align: center;
    padding: 40px 20px;
}

.success-icon {
    font-size: 80px;
    color: #28a745;
    margin-bottom: 24px;
}

.success-title {
    font-size: 24px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 24px;
}

.success-info {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 24px;
    text-align: left;
}

.success-info p {
    margin-bottom: 8px;
    color: #495057;
}

.success-info p:last-child {
    margin-bottom: 0;
}

.success-message {
    color: #6c757d;
    line-height: 1.5;
    margin-bottom: 32px;
}

.success-message p {
    margin-bottom: 8px;
}

.success-message a {
    color: #667eea;
    text-decoration: none;
    font-weight: 500;
}

.back-home-btn, .new-application-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 12px;
    padding: 16px 32px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    margin: 8px;
    min-width: 150px;
}

.new-application-btn {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.back-home-btn:hover, .new-application-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.new-application-btn:hover {
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
}

/* Navigation Buttons */
.navigation-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 32px;
    gap: 12px;
}

.prev-btn {
    background: none;
    border: 2px solid #6c757d;
    color: #6c757d;
    padding: 12px 20px;
    border-radius: 8px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.prev-btn:hover {
    background: #6c757d;
    color: white;
}

.next-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 8px;
    padding: 12px 20px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    flex: 1;
    max-width: 200px;
    margin-left: auto;
}

.next-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.next-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}

/* Notice Section */
.notice-section {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 8px;
    padding: 16px;
    margin-top: 24px;
}

.notice-text {
    margin: 0;
    font-size: 13px;
    color: #856404;
    line-height: 1.4;
}

.notice-text a {
    color: #667eea;
    text-decoration: none;
    font-weight: 500;
}

/* Terms Section */
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

/* Responsive Design */
@media (max-width: 480px) {
    .form-section {
        padding: 0 16px 16px;
    }

    .step-content {
        padding: 16px 0;
    }

    .contact-group {
        padding: 16px;
    }

    .navigation-section {
        flex-direction: column;
        gap: 12px;
    }

    .prev-btn, .next-btn {
        width: 100%;
        max-width: none;
        margin: 0;
    }

    .progress-steps {
        gap: 8px;
    }

    .step-label {
        font-size: 10px;
    }

    .image-preview {
        max-height: 150px;
    }

    .preview-image {
        max-height: 150px;
    }
}

/* Loading Animations */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.fa-spin {
    animation: spin 1s linear infinite;
}

/* Step Animation */
.step-content {
    animation: fadeInUp 0.3s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Progress Animation */
.progress-fill {
    animation: progressFill 0.5s ease-out;
}

@keyframes progressFill {
    from {
        width: 0%;
    }
}

/* Success Animation */
.success-icon {
    animation: successBounce 0.6s ease-out;
}

@keyframes successBounce {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    50% {
        transform: scale(1.1);
        opacity: 1;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.form-hint {
    font-size: 12px;
    color: #6c757d;
    margin-top: 5px;
    line-height: 1.4;
}

/* Enhanced form validation states */
.form-input.is-valid {
    border-color: #28a745;
    box-shadow: 0 0 0 2px rgba(40, 167, 69, 0.1);
}

.form-input.is-valid:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 2px rgba(40, 167, 69, 0.2);
}

/* 圖片預覽動畫 */
.image-preview-container {
    animation: slideInDown 0.4s ease-out;
}

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* 移除按鈕動畫 */
.remove-image-btn {
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.8);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}
    </style>

    <script>
document.addEventListener('DOMContentLoaded', function() {
    // File upload preview handling is now handled by Livewire
    // Keep other JavaScript functionality

    // Phone number formatting
    const phoneInputs = document.querySelectorAll('.phone-input');

    phoneInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            // Remove all non-digit characters
            this.value = this.value.replace(/[^0-9]/g, '');

            // Limit to 10 digits
            if (this.value.length > 10) {
                this.value = this.value.slice(0, 10);
            }
        });
    });

    // Form validation feedback
    const inputs = document.querySelectorAll('.form-input, .form-select');

    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value.trim() !== '') {
                this.classList.add('is-valid');
                this.classList.remove('is-invalid');
            }
        });

        input.addEventListener('invalid', function() {
            this.classList.add('is-invalid');
            this.classList.remove('is-valid');
        });
    });

    // Smooth scroll to top when step changes
    window.addEventListener('livewire:navigated', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
});

// Livewire hooks
document.addEventListener('livewire:init', () => {
    Livewire.on('step-changed', (step) => {
        // Scroll to top when step changes
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });

        // Update page title
        const stepTitles = {
            1: '基本資料',
            2: '緊急聯絡人',
            3: '證件上傳',
            4: '銀行資訊',
            5: '申請完成'
        };

        document.title = `貸款申請 - ${stepTitles[step]}`;
    });

    Livewire.on('file-uploaded', (fileName) => {
        // Show success message for file upload
        console.log('檔案上傳成功:', fileName);
    });

    Livewire.on('validation-error', (errors) => {
        // Handle validation errors
        console.log('驗證錯誤:', errors);
    });

    Livewire.on('new-application-started', () => {
        // 新申請開始時的處理
        console.log('新申請已開始');

        // 可以選擇滾動到頂部
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });

        // 或者顯示提示訊息
        // alert('新申請已開始，請填寫資料');
    });
});
    </script>
</div>
