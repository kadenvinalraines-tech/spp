<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillDetail extends Model
{
    protected $guarded = ['id'];

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    public function isDue(): bool
    {
        $now = now()->startOfMonth();

        if (!$this->month) {
            // not a monthly bill, check if the bill's due_date month has arrived
            if ($this->bill && $this->bill->due_date) {
                $dueDate = \Carbon\Carbon::parse($this->bill->due_date)->startOfMonth();
                return $now->greaterThanOrEqualTo($dueDate);
            }
            return true;
        }
        
        $academicYear = $this->bill->academicYear;
        if (!$academicYear || !$academicYear->start_date) return true;
        
        $startYear = \Carbon\Carbon::parse($academicYear->start_date)->year;
        
        // Month 7-12 are in startYear, Month 1-6 are in startYear + 1
        $billYear = ($this->month >= 7 && $this->month <= 12) ? $startYear : $startYear + 1;
        
        $billDate = \Carbon\Carbon::createFromDate($billYear, $this->month, 1)->startOfMonth();
        
        return $now->greaterThanOrEqualTo($billDate);
    }
}
