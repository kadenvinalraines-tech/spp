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
}
