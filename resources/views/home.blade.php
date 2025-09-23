<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>貸款申請</title>
    <link rel="stylesheet" href="{{ asset('css/loan.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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
    </style>
</head>
<body>
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
                    <img src="{{ asset('images/service-icon.png') }}" alt="高效服務">                </div>
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
                    恭喜您：今日已成功貸款 <span class="loan-count" id="loanCount">32</span> 人，系統正在為您匹配最佳貸款方案...
                </div>
            </div>
        </div>

        <!-- Main Content Layout for Desktop -->
        <div class="main-content">
            <!-- Loan Amount Section -->
            <div class="loan-section">
                <h2>可申請金額</h2>
                <div class="amount-display">$7,000~10萬</div>

                <!-- Slider -->
                <div class="slider-container">
                    <input type="range" class="slider" min="7000" max="100000" value="20000" id="loanSlider">
                </div>

                <div class="loan-info">
                    <div class="info-item">
                        <span class="label">請滑動選擇申請額度：</span>
                        <span class="value" id="selectedAmount">$20,000</span>
                    </div>

                    <div class="loan-details">
                        <div class="detail-item">
                            <span class="detail-label">最長貸款期期</span>
                            <span class="detail-value">91-365天</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">最低貸款利率</span>
                            <span class="detail-value">0.03%/天</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Apply Section for Desktop -->
            <div class="apply-section">
                <!-- Apply Button -->
                <div class="apply-button-container">
                    <button class="apply-btn" onclick="applyLoan()">
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

        <!-- Bottom Navigation -->
        {{-- <div class="bottom-nav">
            <div class="nav-item active">
                <i class="fas fa-search"></i>
                <span>貸款</span>
            </div>
            <div class="nav-item">
                <i class="fas fa-file-alt"></i>
                <span>訂單</span>
            </div>
            <div class="nav-item">
                <i class="fas fa-user"></i>
                <span>我的</span>
            </div>
            <div class="nav-item">
                <i class="fas fa-cog"></i>
                <span>資質</span>
            </div>
        </div> --}}
    </div>

    <script>
        // Slider functionality
        const slider = document.getElementById('loanSlider');
        const selectedAmount = document.getElementById('selectedAmount');

        slider.oninput = function() {
            selectedAmount.textContent = '$' + parseInt(this.value).toLocaleString();
        }

        function applyLoan() {
            const amount = document.getElementById('loanSlider').value;

            // 創建表單並提交
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/apply';

            // 添加 CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // 添加金額參數
            const amountInput = document.createElement('input');
            amountInput.type = 'hidden';
            amountInput.name = 'amount';
            amountInput.value = amount;

            form.appendChild(csrfInput);
            form.appendChild(amountInput);

            // 添加到頁面並提交
            document.body.appendChild(form);
            form.submit();
        }

        // 動態計算貸款人數的函數
        function calculateLoanCount() {
            const now = new Date();
            const hours = now.getHours();
            const minutes = now.getMinutes();

            // 基數：204人（上午9點的基數）
            let baseCount = 32;

            // 計算從上午9點開始到現在的總分鐘數
            let minutesFromStart = 0;

            if (hours >= 9) {
                minutesFromStart = (hours - 9) * 60 + minutes;
            } else {
                // 如果是早上9點前，顯示前一天最終數據
                minutesFromStart = 0;
                baseCount = 380 + Math.floor(Math.random() * 20); // 前一天的數據
            }

            // 每小時平均增加12-18人，轉換為每分鐘0.2-0.3人
            const incrementPerMinute = 0.2 + Math.random() * 0.1;
            const totalIncrement = Math.floor(minutesFromStart * incrementPerMinute);

            // 加入一些隨機波動
            const randomFactor = Math.floor(Math.random() * 10) - 5;

            return Math.max(baseCount + totalIncrement + randomFactor, baseCount);
        }

        // 更新貸款人數
        function updateLoanCount() {
            const loanCountElement = document.getElementById('loanCount');
            const newCount = calculateLoanCount();

            // 添加更新動畫效果
            loanCountElement.classList.add('updating');

            setTimeout(() => {
                loanCountElement.textContent = newCount;
                loanCountElement.classList.remove('updating');

                // 添加閃爍效果
                loanCountElement.parentElement.classList.add('blink');
                setTimeout(() => {
                    loanCountElement.parentElement.classList.remove('blink');
                }, 800);
            }, 250);
        }

        // 初始化
        document.addEventListener('DOMContentLoaded', function() {
            // 立即更新一次
            updateLoanCount();

            // 每3-5分鐘更新一次
            setInterval(updateLoanCount, 3 * 60 * 1000 + Math.random() * 2 * 60 * 1000);

            // 每30秒檢查一次時間，確保數據的準確性
            setInterval(() => {
                const currentCount = parseInt(document.getElementById('loanCount').textContent);
                const calculatedCount = calculateLoanCount();

                // 如果差異太大，重新計算
                if (Math.abs(currentCount - calculatedCount) > 5) {
                    updateLoanCount();
                }
            }, 30 * 1000);
        });

        // 頁面可見性API - 當用戶切換回頁面時更新數據
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                setTimeout(updateLoanCount, 1000);
            }
        });
    </script>
</body>
</html>
