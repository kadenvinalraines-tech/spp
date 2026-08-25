<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Auditable;

class FinancePost extends Model
{
    use Auditable;

    protected $guarded = ['id'];

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }

    public function exemptedStudents(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'student_fee_exemptions', 'finance_post_id', 'student_id')->withTimestamps();
    }
}
