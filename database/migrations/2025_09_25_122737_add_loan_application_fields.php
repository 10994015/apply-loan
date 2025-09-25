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
            $table->string('address', 255)->after('city'); // 居住地址
            // 緊急聯絡人資訊 (第一人)
            $table->string('emergency_contact_1_name', 50)->nullable()->after('line_id');
            $table->string('emergency_contact_1_phone', 20)->nullable()->after('emergency_contact_1_name');
            $table->enum('emergency_contact_1_relationship', ['父親', '母親', '兄弟', '姊妹'])->nullable()->after('emergency_contact_1_phone');

            // 緊急聯絡人資訊 (第二人)
            $table->string('emergency_contact_2_name', 50)->nullable()->after('emergency_contact_1_relationship');
            $table->string('emergency_contact_2_phone', 20)->nullable()->after('emergency_contact_2_name');
            $table->enum('emergency_contact_2_relationship', ['父親', '母親', '兄弟', '姊妹'])->nullable()->after('emergency_contact_2_phone');

            // 身分證件上傳路徑
            $table->string('id_card_front_path', 255)->nullable()->after('emergency_contact_2_relationship'); // 身分證正面
            $table->string('id_card_back_path', 255)->nullable()->after('id_card_front_path'); // 身分證反面
            $table->string('id_card_selfie_path', 255)->nullable()->after('id_card_back_path'); // 手持身分證自拍
            $table->string('second_document_path', 255)->nullable()->after('id_card_selfie_path'); // 第二證件

            // 銀行資訊上傳 (選填)
            $table->string('bank_card_path', 255)->nullable()->after('second_document_path'); // 銀行卡或存摺正面

            // 申請步驟追蹤
            $table->tinyInteger('current_step')->default(1)->after('bank_card_path'); // 目前步驟 (1-5)
            $table->boolean('step_1_completed')->default(false)->after('current_step'); // 基本資料完成
            $table->boolean('step_2_completed')->default(false)->after('step_1_completed'); // 緊急聯絡人完成
            $table->boolean('step_3_completed')->default(false)->after('step_2_completed'); // 證件上傳完成
            $table->boolean('step_4_completed')->default(false)->after('step_3_completed'); // 銀行資訊完成
            $table->boolean('step_5_completed')->default(false)->after('step_4_completed'); // 申請完成

            // 完成時間戳記
            $table->timestamp('step_1_completed_at')->nullable()->after('step_5_completed');
            $table->timestamp('step_2_completed_at')->nullable()->after('step_1_completed_at');
            $table->timestamp('step_3_completed_at')->nullable()->after('step_2_completed_at');
            $table->timestamp('step_4_completed_at')->nullable()->after('step_3_completed_at');
            $table->timestamp('step_5_completed_at')->nullable()->after('step_4_completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            // 移除緊急聯絡人欄位
            $table->dropColumn([
                'emergency_contact_1_name',
                'emergency_contact_1_phone',
                'emergency_contact_1_relationship',
                'emergency_contact_2_name',
                'emergency_contact_2_phone',
                'emergency_contact_2_relationship'
            ]);

            // 移除證件上傳欄位
            $table->dropColumn([
                'id_card_front_path',
                'id_card_back_path',
                'id_card_selfie_path',
                'second_document_path',
                'bank_card_path'
            ]);

            // 移除步驟追蹤欄位
            $table->dropColumn([
                'current_step',
                'step_1_completed',
                'step_2_completed',
                'step_3_completed',
                'step_4_completed',
                'step_5_completed',
                'step_1_completed_at',
                'step_2_completed_at',
                'step_3_completed_at',
                'step_4_completed_at',
                'step_5_completed_at'
            ]);
        });
    }
};
