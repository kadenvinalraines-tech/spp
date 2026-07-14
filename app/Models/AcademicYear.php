<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class AcademicYear extends Model
{
    use Auditable;

    protected $guarded = ['id'];

    public function financePosts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FinancePost::class);
    }

    /**
     * Dapatkan daftar ID tahun ajaran yang memiliki nama sama dengan ID yang diberikan.
     * Berguna untuk menyatukan Ganjil dan Genap dalam satu rentang kelas.
     */
    public static function getSameYearIds($id)
    {
        if (!$id) return [];
        $year = self::find($id);
        if (!$year) return [];
        return self::where('name', $year->name)->pluck('id')->toArray();
    }

    /**
     * Get the active academic year ID safely (verifies it still exists in DB).
     */
    public static function getActiveId()
    {
        $id = session('active_academic_year_id');
        if ($id && self::where('id', $id)->exists()) {
            return $id;
        }
        $actualActive = self::where('is_active', true)->value('id');
        session(['active_academic_year_id' => $actualActive]);
        return $actualActive;
    }
}
