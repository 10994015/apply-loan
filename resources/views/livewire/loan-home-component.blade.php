<div>
    <div class="loan-app">
        <!-- Header Alert -->
        <div class="alert-banner">
            <i class="fas fa-exclamation-triangle"></i>
            今日撥款通道正常，5~15分鐘內可撥款到帳
        </div>

        <!-- Warning Notice -->
        <div class="warning-notice">
            <i class="fas fa-lock"></i>
            <a href="https://line.me/ti/p/sxmWNW-UtV">如需詳細說明，請聯繫LINE專員</a>
        </div>

        <!-- Service Cards -->
        <div class="service-cards">
            <div class="service-card service-card--primary">
                <div class="service-card__content">
                    <h3>高效服務</h3>
                    <p>24小時服務 5分鐘到帳</p>
                </div>
                <div class="service-card__icon">
                    <img src="{{ asset('images/service-icon.png') }}" alt="高效服務">
                </div>
            </div>

            <div class="service-card service-card--secondary">
                <div class="service-card__content">
                    <h3>免照會</h3>
                    <p>在線審查 流程簡潔</p>
                </div>
                <div class="service-card__icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
            </div>

            <div class="service-card service-card--info">
                <div class="service-card__content">
                    <h3>全網路操作</h3>
                    <p>網路操作 方便快速</p>
                </div>
                <div class="service-card__icon">
                    <i class="fas fa-globe"></i>
                </div>
            </div>
        </div>

        <!-- Success Stats with Marquee Effect -->
        <div class="success-stats">
            <i class="fas fa-volume-up"></i>
            <div class="marquee-container">
                <div class="marquee-text">
                    恭喜您：今日已成功貸款 <span class="loan-count" id="loanCount">{{ $loanCount }}</span> 人，系統正在為您匹配最佳貸款方案...
                </div>
            </div>
        </div>

        <!-- Main Content Layout for Desktop -->
        <div class="main-content">
            <!-- Loan Amount Section -->
            <div class="loan-section">
                <h2>可申請金額</h2>
                <div class="amount-display">{{ $this->getAmountRangeDisplay() }}</div>

                <!-- Slider -->
                <div class="slider-container">
                    <input
                        type="range"
                        class="slider"
                        min="{{ $minAmount }}"
                        max="{{ $maxAmount }}"
                        step="1000"
                        wire:model.live="selectedAmount"
                        id="loanSlider"
                    >
                </div>

                <div class="loan-info">
                    <!-- 改進的金額輸入區塊 -->
                    <div class="amount-input-group">
                        <label class="amount-label">請選擇或輸入申請額度：</label>
                        <div class="amount-input-wrapper">
                            <span class="currency-symbol">$</span>
                            <input
                                type="text"
                                wire:model.blur="selectedAmount"
                                class="amount-input"
                                placeholder="請輸入金額"
                                id="amountInput"
                            >
                            <span class="amount-unit">元</span>
                        </div>
                        <div class="amount-hint">
                            可申請範圍：${{ number_format($minAmount) }} ~ ${{ number_format($maxAmount) }}
                        </div>
                    </div>

                    <div class="loan-details">
                        <div class="detail-item">
                            <span class="detail-label">最長貸款期期</span>
                            <span class="detail-value">{{ $this->getLoanPeriodDisplay() }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">最低貸款利率</span>
                            <span class="detail-value">{{ $this->getDailyRateDisplay() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Apply Section for Desktop -->
            <div class="apply-section">
                <!-- Apply Button -->
                <div class="apply-button-container">
                    <button class="apply-btn" wire:click="applyLoan">
                        申請貸款 (24小時服務)
                    </button>
                </div>

                <!-- Partnership Info -->
                <div class="partnership-info">
                    協助媒合多家銀行等金融機構債資款
                </div>

                <!-- Features Section -->
                <div class="features-section">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-robot"></i>
                        </div>
                        <div class="feature-badge">智能AI</div>
                        <div class="feature-text">
                            <strong>專業大數據</strong><br>
                            快速媒合
                        </div>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="feature-badge">4.9星服務</div>
                        <div class="feature-text">
                            <strong>5分好評</strong><br>
                            超過3800則
                        </div>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="feature-badge">業界最多</div>
                        <div class="feature-text">
                            <strong>服務超過</strong><br>
                            250萬人次
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Services -->
        <div class="additional-services">
            <div class="service-feature">
                <div class="service-feature__icon">
                    <i class="fas fa-phone"></i>
                </div>
                <div class="service-feature__badge">免費諮詢</div>
                <div class="service-feature__text">
                    免費LINE諮詢
                </div>
            </div>

            <div class="service-feature">
                <div class="service-feature__icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="service-feature__badge">收費合理</div>
                <div class="service-feature__text">
                    不會後加價<br>
                </div>
            </div>

            <div class="service-feature">
                <div class="service-feature__icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="service-feature__badge">安全無虞</div>
                <div class="service-feature__text">
                    服務過程<br>
                    可全程保密
                </div>
            </div>
        </div>

        <!-- Loan Types -->
        <div class="loan-types">
            <h3>多元貸款方案<br>滿足資金需求</h3>

            <div class="loan-types-grid">
                <div class="loan-type">
                    <div class="loan-type__icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="loan-type__text">信用貸款</div>
                </div>

                <div class="loan-type">
                    <div class="loan-type__icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <div class="loan-type__text">整合負債</div>
                </div>

                <div class="loan-type">
                    <div class="loan-type__icon">
                        <i class="fas fa-car"></i>
                    </div>
                    <div class="loan-type__text">房屋貸款</div>
                </div>

                <div class="loan-type">
                    <div class="loan-type__icon">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <div class="loan-type__text">汽機車貸款</div>
                </div>

                <div class="loan-type">
                    <div class="loan-type__icon">
                        <i class="fas fa-university"></i>
                    </div>
                    <div class="loan-type__text">債務協商</div>
                </div>

                <div class="loan-type">
                    <div class="loan-type__icon">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <div class="loan-type__text">企業貸款</div>
                </div>
            </div>
        </div>

        <!-- Certifications -->
        <div class="certifications">
            <h3>專業認證</h3>
            <div class="cert-grid">
                <div class="cert-item">
                    <div class="cert-icon">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <div class="cert-text">
                        <strong>ISO 27001</strong><br>
                        資安國際認證
                    </div>
                </div>

                <div class="cert-item">
                    <div class="cert-icon">
                        <i class="fas fa-shield"></i>
                    </div>
                    <div class="cert-text">
                        <strong>業界TT05金庫</strong><br>
                        資訊通訊員業
                    </div>
                </div>

                <div class="cert-item">
                    <div class="cert-icon">
                        <i class="fas fa-digital-tachograph"></i>
                    </div>
                    <div class="cert-text">
                        <strong>創新村落</strong><br>
                        數位貸款平台
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link href="{{ asset('css/loan.css') }}" rel="stylesheet">
    @endpush

    <style>
        /* 跑馬燈效果樣式 */
        .success-stats {
            margin: 0 16px 20px 16px;
            padding: 16px 20px;
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border-radius: 12px;
            color: #92400e;
            font-size: 15px;
            font-weight: 600;
            display: flex;
            align-items: center;
            border: 1px solid #fbbf24;
            position: relative;
            overflow: hidden;
            white-space: nowrap;
        }

        .success-stats::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, #f59e0b, #fbbf24, #f59e0b);
        }

        .success-stats i {
            margin-right: 12px;
            font-size: 18px;
            color: #f59e0b;
            z-index: 1;
            position: relative;
            flex-shrink: 0;
        }

        .marquee-container {
            flex: 1;
            overflow: hidden;
            position: relative;
        }

        .marquee-text {
            display: inline-block;
            animation: marquee 15s linear infinite;
            white-space: nowrap;
        }

        .loan-count {
            color: #dc2626;
            font-weight: 800;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .loan-count.updating {
            animation: countUpdate 0.5s ease-in-out;
        }

        @keyframes marquee {
            0% {
                transform: translateX(100%);
            }
            100% {
                transform: translateX(-100%);
            }
        }

        @keyframes countUpdate {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); color: #ef4444; }
            100% { transform: scale(1); }
        }

        /* 當滑鼠懸停時暫停動畫 */
        .success-stats:hover .marquee-text {
            animation-play-state: paused;
        }

        /* 閃爍效果 */
        .blink {
            animation: blink 0.8s ease-in-out;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        /* 載入CSS檔案或重複原有樣式 */
        @import url('{{ asset("css/loan.css") }}');
        .amount-input-group {
    margin-bottom: 20px;
    padding: 20px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 12px;
    border: 2px solid #dee2e6;
}

.amount-label {
    display: block;
    font-size: 15px;
    font-weight: 600;
    color: #495057;
    margin-bottom: 12px;
}

.amount-input-wrapper {
    display: flex;
    align-items: center;
    background: white;
    border: 2px solid #ddd;
    border-radius: 10px;
    padding: 4px 16px;
    transition: all 0.3s ease;
    position: relative;
}

.amount-input-wrapper:focus-within {
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.currency-symbol {
    font-size: 20px;
    font-weight: 700;
    color: #667eea;
    margin-right: 8px;
}

.amount-input {
    flex: 1;
    border: none;
    outline: none;
    font-size: 24px;
    font-weight: 700;
    color: #2c3e50;
    padding: 12px 8px;
    text-align: center;
    min-width: 0;
}

.amount-input::placeholder {
    color: #adb5bd;
    font-weight: 400;
}

.amount-unit {
    font-size: 16px;
    font-weight: 600;
    color: #6c757d;
    margin-left: 8px;
}

.amount-hint {
    margin-top: 8px;
    font-size: 13px;
    color: #6c757d;
    text-align: center;
}

/* 錯誤狀態 */
.amount-input-wrapper.error {
    border-color: #dc3545;
    animation: shake 0.3s ease-in-out;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-10px); }
    75% { transform: translateX(10px); }
}

/* 滑桿樣式增強 */
.slider-container {
    margin: 24px 0;
    padding: 0 10px;
}

.slider {
    width: 100%;
    height: 8px;
    border-radius: 5px;
    background: linear-gradient(to right, #e9ecef 0%, #667eea 100%);
    outline: none;
    -webkit-appearance: none;
}

.slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    transition: all 0.3s ease;
}

.slider::-webkit-slider-thumb:hover {
    transform: scale(1.15);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
}

.slider::-webkit-slider-thumb:active {
    transform: scale(1.05);
}

.slider::-moz-range-thumb {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    cursor: pointer;
    border: none;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    transition: all 0.3s ease;
}

.slider::-moz-range-thumb:hover {
    transform: scale(1.15);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
}

/* 響應式設計 */
@media (max-width: 768px) {
    .amount-input {
        font-size: 20px;
    }

    .currency-symbol {
        font-size: 18px;
    }

    .amount-unit {
        font-size: 14px;
    }

    .amount-input-group {
        padding: 16px;
    }
}
    </style>

  <script>
document.addEventListener('DOMContentLoaded', function() {
    const amountInput = document.getElementById('amountInput');
    const slider = document.getElementById('loanSlider');

    if (amountInput) {
        // 格式化輸入的金額（加上千分位）
        amountInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/,/g, '');

            // 只允許數字
            value = value.replace(/[^\d]/g, '');

            if (value) {
                // 加上千分位
                e.target.value = parseInt(value).toLocaleString();
            }
        });

        // 當焦點離開時驗證金額
        amountInput.addEventListener('blur', function(e) {
            let value = e.target.value.replace(/,/g, '');
            let numValue = parseInt(value);

            const minAmount = parseInt('{{ $minAmount }}');
            const maxAmount = parseInt('{{ $maxAmount }}');
            const wrapper = e.target.closest('.amount-input-wrapper');

            if (isNaN(numValue) || numValue < minAmount) {
                numValue = minAmount;
                wrapper.classList.add('error');
                setTimeout(() => wrapper.classList.remove('error'), 500);
            } else if (numValue > maxAmount) {
                numValue = maxAmount;
                wrapper.classList.add('error');
                setTimeout(() => wrapper.classList.remove('error'), 500);
            }

            // 調整到最接近的千位數
            numValue = Math.round(numValue / 1000) * 1000;

            e.target.value = numValue.toLocaleString();

            // 更新 Livewire
            @this.set('selectedAmount', numValue);
        });

        // 當按下 Enter 時移除焦點
        amountInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.target.blur();
            }
        });
    }

    // 監聽 Livewire 更新，同步更新輸入框
    document.addEventListener('livewire:updated', function() {
        if (amountInput) {
            const currentAmount = @this.get('selectedAmount');
            if (currentAmount) {
                amountInput.value = parseInt(currentAmount).toLocaleString();
            }
        }
    });
});
</script>
</div>
