<x-admin-layout title="會員中心">
    <div class="admin-container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="header-content">
                <h1 class="page-title">
                    <i class="fas fa-user-cog"></i>
                    會員中心
                </h1>
                <p class="page-subtitle">管理您的個人資料和帳戶設定</p>
            </div>
        </div>

        <!-- Profile Content -->
        <div class="profile-content">
            <div class="max-w-4xl mx-auto">
                @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                    <div class="profile-section">
                        <div class="section-header">
                            <h2 class="section-title">
                                <i class="fas fa-user"></i>
                                個人資料
                            </h2>
                        </div>
                        <div class="section-content">
                            @livewire('profile.update-profile-information-form')
                        </div>
                    </div>
                @endif

                @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                    <div class="profile-section">
                        <div class="section-header">
                            <h2 class="section-title">
                                <i class="fas fa-key"></i>
                                更改密碼
                            </h2>
                        </div>
                        <div class="section-content">
                            @livewire('profile.update-password-form')
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .admin-container {
            min-height: 100vh;
            background: #f8fafc;
        }

        .page-header {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 32px 0;
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }

        .page-title {
            font-size: 32px;
            font-weight: 700;
            color: #1f2937;
            margin: 0 0 8px 0;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .page-title i {
            color: #3b82f6;
        }

        .page-subtitle {
            font-size: 16px;
            color: #6b7280;
            margin: 0;
        }

        .profile-content {
            padding: 40px 24px;
        }

        .profile-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 32px;
            overflow: hidden;
        }

        .section-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 24px 32px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: white;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-title i {
            color: white;
            font-size: 18px;
        }

        .section-content {
            padding: 32px;
        }

        /* 美化 Jetstream 表單樣式 */
        .profile-section form {
            max-width: none;
        }

        .profile-section .col-span-6 {
            margin-bottom: 20px;
        }

        .profile-section label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        .profile-section input[type="text"],
        .profile-section input[type="email"],
        .profile-section input[type="password"] {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 16px;
            background: white;
            transition: all 0.3s ease;
            color: #1f2937;
        }

        .profile-section input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .profile-section input.input-error {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        .profile-section .input-error {
            color: #ef4444;
            font-size: 12px;
            margin-top: 4px;
        }

        .profile-section .text-sm {
            font-size: 13px;
            color: #6b7280;
            margin-top: 4px;
        }

        /* 按鈕樣式 */
        .profile-section button[type="submit"],
        .profile-section .inline-flex.items-center.px-4 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .profile-section button[type="submit"]:hover,
        .profile-section .inline-flex.items-center.px-4:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .profile-section button[type="submit"]:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* 成功/錯誤訊息樣式 */
        .profile-section .font-medium.text-sm.text-green-600 {
            background: #f0f9f4;
            color: #059669;
            padding: 12px 16px;
            border-radius: 8px;
            border-left: 4px solid #10b981;
            margin-bottom: 20px;
        }

        .profile-section .font-medium.text-sm.text-red-600 {
            background: #fef2f2;
            color: #dc2626;
            padding: 12px 16px;
            border-radius: 8px;
            border-left: 4px solid #ef4444;
            margin-bottom: 20px;
        }

        /* 頭像上傳區域 */
        .profile-section .mt-2.flex.items-center {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            border: 2px dashed #d1d5db;
            margin: 16px 0;
        }

        .profile-section .rounded-full {
            border: 3px solid #e5e7eb;
        }

        /* Grid 佈局改進 */
        .profile-section .grid.grid-cols-6.gap-6 {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .profile-section .col-span-6.sm\\:col-span-4 {
            grid-column: 1;
        }

        /* 載入動畫 */
        .profile-section .opacity-25 {
            opacity: 0.5;
        }

        /* 分隔線 */
        .profile-section .border-gray-200 {
            border-color: #e5e7eb;
            margin: 24px 0;
        }

        /* 響應式設計 */
        @media (max-width: 768px) {
            .header-content {
                padding: 0 16px;
            }

            .page-title {
                font-size: 24px;
            }

            .profile-content {
                padding: 24px 16px;
            }

            .section-header {
                padding: 20px 24px;
            }

            .section-content {
                padding: 24px 20px;
            }
        }

        /* 深色模式兼容 */
        .dark .profile-section {
            background: #1f2937;
            color: white;
        }

        .dark .profile-section input {
            background: #374151;
            border-color: #4b5563;
            color: white;
        }

        .dark .profile-section label {
            color: #e5e7eb;
        }
    </style>
</x-admin-layout>
