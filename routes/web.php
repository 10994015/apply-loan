<?php

use App\Livewire\InputDataComponent;
use App\Livewire\LoanAdminComponent;
use App\Livewire\LoanHomeComponent;
use App\Livewire\LoanSettingsComponent;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// 首頁 - 使用 Livewire 組件，支援從資料庫讀取金額設定
Route::get('/', LoanHomeComponent::class)->name('loan.index');

// 申請頁面 - 使用 Livewire 組件，支援接收金額參數
Route::get('/apply', InputDataComponent::class)->name('loan.apply');

// 保留 POST 路由以支援舊有的表單提交（如果需要）
Route::post('/apply', function() {
    $amount = request('amount', 20000);
    return redirect()->route('loan.apply', ['amount' => $amount]);
})->name('loan.apply.post');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::prefix('admin')->name('admin.')->group(function () {

        // 儀表板 (重定向到貸款管理)
        Route::get('/', function () {
            return redirect()->route('admin.loans');
        })->name('dashboard');

        // 貸款申請管理
        Route::get('/loans', LoanAdminComponent::class)->name('loans');

        // 貸款設定管理
        Route::get('/settings', LoanSettingsComponent::class)->name('settings');

        // 其他可能的管理功能擴展點
        // Route::get('/reports', ReportsComponent::class)->name('reports');
        // Route::get('/users', UsersComponent::class)->name('users');
    });

});

// 禁用註冊頁面
Route::get('/register', function () {
    abort(404);
});
