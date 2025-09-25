<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? '後台管理' }} - 登您來管理系統</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    @livewireStyles
    @stack('styles')
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="admin-nav">
        <div class="nav-container">
            <div class="nav-brand">
                <i class="fas fa-user-circle"></i>
                <span>登您來管理系統</span>
            </div>
            <div class="nav-menu">
                <a href="{{ route('admin.loans') }}" class="nav-link {{ request()->routeIs('admin.loans') ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i>
                    <span>貸款申請</span>
                </a>
                @if(false)
                <a href="{{ route('admin.settings') }}" class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i>
                    <span>系統設定</span>
                </a>
                @endif
                <a href="{{ route('loan.index') }}" class="nav-link" target="_blank">
                    <i class="fas fa-external-link-alt"></i>
                    <span>前台首頁</span>
                </a>
            </div>
            <div class="nav-user">
                <div class="user-dropdown">
                    <button class="user-info dropdown-toggle" onclick="toggleDropdown()">
                        <i class="fas fa-user"></i>
                        <span>{{ Auth::user()->name ?? '管理員' }}</span>
                    </button>
                    <div class="dropdown-menu" id="userDropdown">
                        <a href="{{ route('profile.show') }}" class="dropdown-item">
                            <i class="fas fa-user-cog"></i>
                            <span>會員中心</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}" class="dropdown-form">
                            @csrf
                            <button type="submit" class="dropdown-item logout-btn">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>登出</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="admin-main">
        {{ $slot }}
    </main>

    @livewireScripts

    <!-- JavaScript for Dropdown -->
    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('show');
        }

        // Close dropdown when clicking outside
        window.onclick = function(event) {
            if (!event.target.matches('.dropdown-toggle') && !event.target.closest('.dropdown-toggle')) {
                const dropdown = document.getElementById('userDropdown');
                if (dropdown.classList.contains('show')) {
                    dropdown.classList.remove('show');
                }
            }
        }

        // Confirmation for dangerous actions
        window.confirmAction = function(message) {
            return confirm(message || '確定要執行此操作嗎？');
        }

        // Auto hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';
                    setTimeout(function() {
                        if (alert.parentNode) {
                            alert.parentNode.removeChild(alert);
                        }
                    }, 300);
                }, 5000);
            });
        });
    </script>
</body>

<style>
/* Navigation Styles */
.admin-nav {
    background: white;
    border-bottom: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    position: sticky;
    top: 0;
    z-index: 1000;
}

.nav-container {
    max-width: 1400px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 20px;
    height: 64px;
}

.nav-brand {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 20px;
    font-weight: 700;
    color: #1f2937;
}

.nav-brand i {
    color: #3b82f6;
    font-size: 24px;
}

.nav-menu {
    display: flex;
    align-items: center;
    gap: 8px;
}

.nav-link {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 16px;
    color: #6b7280;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.2s;
    font-weight: 500;
}

.nav-link:hover {
    background: #f3f4f6;
    color: #3b82f6;
}

.nav-link.active {
    background: #eff6ff;
    color: #3b82f6;
}

.nav-user {
    display: flex;
    align-items: center;
    position: relative;
}

.user-dropdown {
    position: relative;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background: #f3f4f6;
    border-radius: 8px;
    color: #374151;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
    outline: none;
}

.user-info:hover {
    background: #e5e7eb;
}

.dropdown-menu {
    position: absolute;
    top: 80%;
    right: 0;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    min-width: 200px;
    z-index: 1000;
    display: none;
    margin-top: 4px;
}

.dropdown-menu.show {
    display: block;
}

.dropdown-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    color: #374151;
    text-decoration: none;
    transition: background 0.2s;
    border: none;
    background: none;
    width: 100%;
    text-align: left;
    cursor: pointer;
    font-size: 14px;
}

.dropdown-item:hover {
    background: #f3f4f6;
}

.dropdown-item i {
    width: 16px;
    color: #6b7280;
}

.logout-btn {
    color: #dc2626;
}

.logout-btn:hover {
    background: #fee2e2;
}

.logout-btn i {
    color: #dc2626;
}

.dropdown-divider {
    height: 1px;
    background: #e5e7eb;
    margin: 4px 0;
}

.dropdown-form {
    margin: 0;
    padding: 0;
}

.admin-main {
    min-height: calc(100vh - 64px);
    background: #f8fafc;
}

/* Enhanced Alert Animations */
.alert {
    animation: slideInDown 0.3s ease-out;
    transition: all 0.3s ease;
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

/* Responsive */
@media (max-width: 968px) {
    .nav-container {
        padding: 0 12px;
    }

    .nav-menu {
        gap: 4px;
    }

    .nav-link span {
        display: none;
    }

    .nav-brand span {
        display: none;
    }
}

@media (max-width: 768px) {
    .nav-menu {
        display: none;
    }

    .user-info span {
        display: none;
    }

    .dropdown-menu {
        right: -8px;
        min-width: 160px;
    }

    .dropdown-item span {
        display: inline;
    }
}

/* Mobile Menu Toggle (if needed later) */
.mobile-menu-toggle {
    display: none;
    background: none;
    border: none;
    font-size: 20px;
    color: #6b7280;
    cursor: pointer;
    padding: 8px;
}

@media (max-width: 768px) {
    .mobile-menu-toggle {
        display: block;
    }
}
</style>
</html>
