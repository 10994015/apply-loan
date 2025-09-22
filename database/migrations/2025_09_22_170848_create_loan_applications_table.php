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
        Schema::create('loan_applications', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 20)->index(); // 手機號碼
            $table->decimal('amount', 10, 2); // 貸款金額
            $table->enum('status', ['pending', 'approved', 'rejected', 'processing'])
                  ->default('pending'); // 申請狀態
            $table->string('country_code', 5)->default('+886'); // 國碼
            $table->text('notes')->nullable(); // 備註
            $table->timestamp('applied_at')->useCurrent(); // 申請時間
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_applications');
    }
};
