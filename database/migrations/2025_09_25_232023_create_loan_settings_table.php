<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('loan_settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_key')->unique()->comment('設定鍵名');
            $table->string('setting_value')->comment('設定值');
            $table->string('setting_type')->default('string')->comment('設定值類型：string, integer, decimal, boolean');
            $table->string('description')->nullable()->comment('設定描述');
            $table->boolean('is_active')->default(true)->comment('是否啟用');
            $table->timestamps();

            // 索引
            $table->index(['setting_key', 'is_active']);
        });

        // 插入預設資料
        DB::table('loan_settings')->insert([
            [
                'setting_key' => 'loan_min_amount',
                'setting_value' => '7000',
                'setting_type' => 'integer',
                'description' => '貸款最小金額',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'loan_max_amount',
                'setting_value' => '100000',
                'setting_type' => 'integer',
                'description' => '貸款最大金額',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'loan_default_amount',
                'setting_value' => '20000',
                'setting_type' => 'integer',
                'description' => '貸款預設金額',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'loan_min_days',
                'setting_value' => '91',
                'setting_type' => 'integer',
                'description' => '貸款最少天數',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'loan_max_days',
                'setting_value' => '365',
                'setting_type' => 'integer',
                'description' => '貸款最多天數',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'loan_daily_rate',
                'setting_value' => '0.03',
                'setting_type' => 'decimal',
                'description' => '最低日利率(%)',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_settings');
    }
};
