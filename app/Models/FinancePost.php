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
}
