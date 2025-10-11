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
        Schema::table('loan_applications', function (Blueprint $table) {
            // 公司資訊 (放在職業欄位後面)
            $table->string('company_name', 100)->nullable()->after('occupation'); // 公司名稱
            $table->string('company_address', 255)->nullable()->after('company_name'); // 公司地址
            $table->string('company_phone', 20)->nullable()->after('company_address'); // 公司電話

            // 居住門牌照片 (放在地址欄位後面)
            $table->string('residence_photo_path', 255)->nullable()->after('address'); // 居住門牌照片

            // 備註欄位已存在於原始表中，但如果你需要額外的備註欄位，可以這樣命名：
            // $table->text('additional_notes')->nullable()->after('notes'); // 額外備註
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->dropColumn([
                'company_name',
                'company_address',
                'company_phone',
                'residence_photo_path'
                // 'additional_notes' // 如果有新增額外備註欄位
            ]);
        });
    }
};
