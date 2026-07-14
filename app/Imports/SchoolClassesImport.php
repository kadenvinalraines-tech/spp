<?php

namespace App\Imports;

use App\Models\SchoolClass;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class SchoolClassesImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Keys are automatically snake_cased or slugified from the heading names.
            // If the heading is "Nama Kelas", the key will be "nama_kelas".
            // If the heading is "Tingkat (Level)", the key will be "tingkat_level".
            // If the heading is "Jurusan (Major)", the key will be "jurusan_major".
            // We can also allow regular names if user changed it.

            $name = $row['nama_kelas'] ?? $row['name'] ?? null;
            $level = $row['tingkat_level'] ?? $row['level'] ?? null;
            $major = $row['jurusan_major'] ?? $row['major'] ?? null;

            if (empty($name) || empty($level)) {
                continue;
            }

            SchoolClass::updateOrCreate(
                ['name' => $name],
                [
                    'level' => $level,
                    'major' => $major,
                ]
            );
        }
    }
}
