<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LoanApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'occupation',
        'city',
        'contact_time',
        'line_id',
        'amount',
        'status',
        'country_code',
        'notes',
        'applied_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'applied_at' => 'datetime',
    ];

    // 狀態常數
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

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
     * 獲取狀態顏色 (Bootstrap 顏色)
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'processing' => 'info',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * 獲取狀態圖示
     */
    public function getStatusIconAttribute()
    {
        return match($this->status) {
            'pending' => 'fas fa-clock',
            'processing' => 'fas fa-spinner fa-spin',
            'approved' => 'fas fa-check-circle',
            'rejected' => 'fas fa-times-circle',
            default => 'fas fa-question-circle'
        };
    }

    /**
     * 格式化申請時間
     */
    public function getFormattedAppliedAtAttribute()
    {
        return $this->applied_at->format('Y-m-d H:i:s');
    }

    /**
     * 格式化申請時間 (簡短)
     */
    public function getShortAppliedAtAttribute()
    {
        return $this->applied_at->format('m/d H:i');
    }

    /**
     * 獲取申請時間距現在的時間差
     */
    public function getAppliedAtDiffAttribute()
    {
        return $this->applied_at->diffForHumans();
    }

    /**
     * 獲取完整聯絡資訊
     */
    public function getContactInfoAttribute()
    {
        $info = [
            '姓名: ' . $this->name,
            '電話: ' . $this->formatted_phone,
            '職業: ' . $this->occupation,
            '縣市: ' . $this->city,
            '聯繫時間: ' . $this->contact_time,
        ];

        if ($this->line_id) {
            $info[] = 'Line ID: ' . $this->line_id;
        }

        return implode("\n", $info);
    }

    /**
     * 檢查是否為今日申請
     */
    public function getIsTodayAttribute()
    {
        return $this->applied_at->isToday();
    }

    /**
     * 檢查是否可以編輯
     */
    public function getCanEditAttribute()
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    /**
     * 檢查是否可以取消
     */
    public function getCanCancelAttribute()
    {
        return $this->status === 'pending';
    }

    /**
     * Scope: 今日申請
     */
    public function scopeToday($query)
    {
        return $query->whereDate('applied_at', Carbon::today());
    }

    /**
     * Scope: 昨日申請
     */
    public function scopeYesterday($query)
    {
        return $query->whereDate('applied_at', Carbon::yesterday());
    }

    /**
     * Scope: 本週申請
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('applied_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ]);
    }

    /**
     * Scope: 本月申請
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('applied_at', Carbon::now()->month)
                     ->whereYear('applied_at', Carbon::now()->year);
    }

    /**
     * Scope: 待審核
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: 處理中
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    /**
     * Scope: 已核准
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope: 已拒絕
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope: 依狀態篩選
     */
    public function scopeByStatus($query, $status)
    {
        if ($status && $status !== 'all') {
            return $query->where('status', $status);
        }
        return $query;
    }

    /**
     * Scope: 依城市篩選
     */
    public function scopeByCity($query, $city)
    {
        if ($city) {
            return $query->where('city', $city);
        }
        return $query;
    }

    /**
     * Scope: 金額範圍篩選
     */
    public function scopeByAmountRange($query, $minAmount = null, $maxAmount = null)
    {
        if ($minAmount !== null) {
            $query->where('amount', '>=', $minAmount);
        }
        if ($maxAmount !== null) {
            $query->where('amount', '<=', $maxAmount);
        }
        return $query;
    }

    /**
     * Scope: 搜尋 (姓名、電話、Line ID)
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%')
                  ->orWhere('occupation', 'like', '%' . $search . '%')
                  ->orWhere('city', 'like', '%' . $search . '%')
                  ->orWhere('line_id', 'like', '%' . $search . '%')
                  ->orWhere('id', 'like', '%' . $search . '%');
            });
        }
        return $query;
    }

    /**
     * 獲取所有可用的狀態選項
     */
    public static function getStatusOptions()
    {
        return [
            self::STATUS_PENDING => '待審核',
            self::STATUS_PROCESSING => '處理中',
            self::STATUS_APPROVED => '已核准',
            self::STATUS_REJECTED => '已拒絕',
        ];
    }

    /**
     * 獲取台灣縣市選項
     */
    public static function getCityOptions()
    {
        return [
            '台北市', '新北市', '桃園市', '台中市', '台南市', '高雄市',
            '基隆市', '新竹市', '嘉義市', '新竹縣', '苗栗縣', '彰化縣',
            '南投縣', '雲林縣', '嘉義縣', '屏東縣', '宜蘭縣', '花蓮縣',
            '台東縣', '澎湖縣', '金門縣', '連江縣'
        ];
    }

    /**
     * 獲取聯繫時間選項
     */
    public static function getContactTimeOptions()
    {
        return [
            '上午 09:00-12:00',
            '下午 12:00-18:00',
            '晚上 18:00-21:00',
            '平日任何時間',
            '週末任何時間',
            '隨時'
        ];
    }

    /**
     * 更新狀態
     */
    public function updateStatus($status, $notes = null)
    {
        $this->update([
            'status' => $status,
            'notes' => $notes ?: $this->notes
        ]);

        return $this;
    }

    /**
     * 核准申請
     */
    public function approve($notes = null)
    {
        return $this->updateStatus(self::STATUS_APPROVED, $notes);
    }

    /**
     * 拒絕申請
     */
    public function reject($notes = null)
    {
        return $this->updateStatus(self::STATUS_REJECTED, $notes);
    }

    /**
     * 開始處理
     */
    public function startProcessing($notes = null)
    {
        return $this->updateStatus(self::STATUS_PROCESSING, $notes);
    }
}
