<?php

use App\Livewire\InputDataComponent;
use App\Livewire\LoanAdminComponent;
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

Route::get('/', function () {
    return view('home');
})->name('loan.index');;
Route::get('/apply', function() {
    return view('loan-apply');
})->name('loan.apply');
Route::post('/apply', InputDataComponent::class)
    ->name('loan.apply.post');


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::prefix('admin')->name('admin.')->group(function () {

        // 儀表板 (可以先用重定向到貸款管理)
        Route::get('/', function () {
            return redirect()->route('admin.loans');
        })->name('dashboard');

        // 貸款申請管理
        Route::get('/loans', LoanAdminComponent::class)->name('loans');

        // 其他管理功能可以在這裡擴展
        // Route::get('/settings', SettingsComponent::class)->name('settings');
        // Route::get('/reports', ReportsComponent::class)->name('reports');
    });

});
