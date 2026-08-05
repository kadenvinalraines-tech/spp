<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Auditable;

class Bill extends Model
{
    use Auditable;

    protected $guarded = ['id'];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function financePost(): BelongsTo
    {
        return $this->belongsTo(FinancePost::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(BillDetail::class);
    }

    public function getDueReminderStatusAttribute()
    {
        if ($this->status === 'paid') {
            return null; // Tidak perlu status jika sudah lunas
        }

        if (!$this->due_date) {
            return null;
        }

        $today = \Carbon\Carbon::today();
        $dueDate = \Carbon\Carbon::parse($this->due_date)->startOfDay();

        if ($dueDate->isToday()) {
            return ['text' => 'Jatuh tempo hari ini', 'class' => 'text-warning'];
        } elseif ($dueDate->isYesterday()) {
            return ['text' => 'Terlambat 1 hari', 'class' => 'text-danger fw-bold'];
        } elseif ($dueDate->isTomorrow()) {
            return ['text' => 'Jatuh tempo besok', 'class' => 'text-warning'];
        } elseif ($dueDate->isPast()) {
            $days = $dueDate->diffInDays($today);
            return ['text' => "Terlambat {$days} hari", 'class' => 'text-danger fw-bold'];
        } else {
            $days = $today->diffInDays($dueDate);
            return ['text' => "Jatuh tempo {$days} hari lagi", 'class' => 'text-muted'];
        }
    }
}
