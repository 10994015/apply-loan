<x-guest-layout>
    <div class="loan-login-container">
        <!-- Header -->
        <div class="header">
            <button class="back-btn" onclick="window.location.href='{{ route('loan.index') ?? '/' }}'">
                <i class="fas fa-chevron-left"></i>
            </button>
            <h1 class="header-title">管理員登入</h1>
        </div>

        <!-- Logo Section -->
        <div class="logo-section">
            <div class="logo">
                <div class="logo-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
            </div>
        </div>

        <!-- Welcome Text -->
        <div class="welcome-section">
            <h2 class="welcome-title">登您來管理系統</h2>
            <p class="welcome-subtitle">請輸入您的帳號密碼以進入系統</p>
        </div>

        <!-- Status Messages -->
        <x-validation-errors class="mb-4" />

        @if (session('status'))
            <div class="status-message">
                <i class="fas fa-info-circle"></i>
                {{ session('status') }}
            </div>
        @endif

        <!-- Form Section -->
        <div class="form-section">
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Username Input -->
                <div class="form-group">
                    <label class="form-label">帳號</label>
                    <div class="input-group">
                        <i class="fas fa-user input-icon"></i>
                        <input
                            id="username"
                            type="text"
                            name="username"
                            value="{{ old('username') }}"
                            class="form-input @error('username') error @enderror"
                            placeholder="請輸入您的帳號"
                            required
                            autofocus
                            autocomplete="username"
                        >
                    </div>
                    @error('username')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password Input -->
                <div class="form-group">
                    <label class="form-label">密碼</label>
                    <div class="input-group">
                        <i class="fas fa-lock input-icon"></i>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            class="form-input @error('password') error @enderror"
                            placeholder="請輸入您的密碼"
                            required
                            autocomplete="current-password"
                        >
                        <button type="button" class="toggle-password" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="form-group remember-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="remember_me" name="remember" class="checkbox-input">
                        <span class="checkbox-custom"></span>
                        <span class="checkbox-text">記住我的登入狀態</span>
                    </label>
                </div>

                <!-- Login Button -->
                <div class="submit-section">
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-sign-in-alt"></i>
                        登入系統
                    </button>
                </div>

                <!-- Forgot Password -->
                @if (Route::has('password.request'))
                    <div class="forgot-password">
                        <a href="{{ route('password.request') }}" class="forgot-link">
                            <i class="fas fa-question-circle"></i>
                            忘記密碼？
                        </a>
                    </div>
                @endif
            </form>
        </div>

        <!-- Footer -->
        <div class="login-footer">
            <p class="footer-text">
                © 2024 登您來貸款系統 - 管理後台
            </p>
        </div>
    </div>

    <style>
        .loan-login-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            flex-direction: column;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }

        .back-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 12px;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-right: 16px;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-1px);
        }

        .header-title {
            color: white;
            font-size: 24px;
            font-weight: 600;
            margin: 0;
        }

        .logo-section {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }

        .logo {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .logo-icon {
            font-size: 32px;
            color: white;
        }

        .welcome-section {
            text-align: center;
            margin-bottom: 40px;
        }

        .welcome-title {
            color: white;
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 8px 0;
        }

        .welcome-subtitle {
            color: rgba(255, 255, 255, 0.8);
            font-size: 16px;
            margin: 0;
            line-height: 1.5;
        }

        .status-message {
            background: rgba(76, 175, 80, 0.9);
            color: white;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-section {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 40px 32px;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
            font-size: 15px;
        }

        .input-group {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            color: #6c757d;
            font-size: 16px;
            z-index: 2;
        }

        .form-input {
            width: 100%;
            padding: 16px 16px 16px 48px;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            font-size: 16px;
            background: white;
            transition: all 0.3s ease;
            color: #2c3e50;
        }

        .form-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: translateY(-1px);
        }

        .form-input.error {
            border-color: #e74c3c;
            box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
        }

        .toggle-password {
            position: absolute;
            right: 16px;
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            padding: 4px;
            font-size: 16px;
            z-index: 2;
        }

        .toggle-password:hover {
            color: #495057;
        }

        .error-message {
            display: block;
            margin-top: 6px;
            color: #e74c3c;
            font-size: 13px;
            font-weight: 500;
        }

        .remember-group {
            margin-bottom: 32px;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            cursor: pointer;
            font-size: 14px;
            color: #495057;
        }

        .checkbox-input {
            display: none;
        }

        .checkbox-custom {
            width: 20px;
            height: 20px;
            border: 2px solid #dee2e6;
            border-radius: 4px;
            margin-right: 12px;
            position: relative;
            transition: all 0.3s ease;
        }

        .checkbox-input:checked + .checkbox-custom {
            background: #667eea;
            border-color: #667eea;
        }

        .checkbox-input:checked + .checkbox-custom::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 12px;
            font-weight: bold;
        }

        .checkbox-text {
            font-weight: 500;
        }

        .submit-section {
            margin-bottom: 24px;
        }

        .submit-btn {
            width: 100%;
            padding: 18px;
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
            gap: 10px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .forgot-password {
            text-align: center;
        }

        .forgot-link {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.3s ease;
        }

        .forgot-link:hover {
            color: #5a67d8;
            transform: translateY(-1px);
        }

        .login-footer {
            text-align: center;
            margin-top: auto;
            padding-top: 20px;
        }

        .footer-text {
            color: rgba(255, 255, 255, 0.7);
            font-size: 13px;
            margin: 0;
        }

        /* 響應式設計 */
        @media (max-width: 768px) {
            .loan-login-container {
                padding: 16px;
            }

            .form-section {
                padding: 32px 24px;
            }

            .welcome-title {
                font-size: 24px;
            }

            .welcome-subtitle {
                font-size: 14px;
            }
        }

        /* 深色模式支援 */
        @media (prefers-color-scheme: dark) {
            .form-section {
                background: rgba(30, 30, 30, 0.95);
            }

            .form-label {
                color: #e9ecef;
            }

            .form-input {
                background: rgba(40, 40, 40, 0.8);
                color: #e9ecef;
                border-color: rgba(255, 255, 255, 0.1);
            }

            .checkbox-text {
                color: #e9ecef;
            }
        }
    </style>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // 添加輸入動畫效果
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.form-input');

            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'translateY(-2px)';
                });

                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</x-guest-layout>
