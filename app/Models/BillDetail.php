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
}
