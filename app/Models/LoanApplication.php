<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LoanApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'amount',
        'status',
        'country_code',
        'notes',
        'applied_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'applied_at' => 'datetime',
    ];

    /**
     * 格式化手機號碼顯示
     */
    public function getFormattedPhoneAttribute()
    {
        return $this->country_code . ' ' . $this->phone;
    }

    /**
     * 格式化金額顯示
     */
    public function getFormattedAmountAttribute()
    {
        return '$' . number_format($this->amount, 0);
    }

    /**
     * 獲取狀態中文名稱
     */
    public function getStatusNameAttribute()
    {
        return match($this->status) {
            'pending' => '待審核',
            'approved' => '已核准',
            'rejected' => '已拒絕',
            'processing' => '處理中',
            default => '未知狀態'
        };
    }

    /**
     * Scope: 今日申請
     */
    public function scopeToday($query)
    {
        return $query->whereDate('applied_at', Carbon::today());
    }

    /**
     * Scope: 待審核
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
