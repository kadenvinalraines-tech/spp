<?php

namespace App\Exports;

use App\Models\SchoolClass;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SchoolClassesExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return SchoolClass::all();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Tingkat (Level)',
            'Nama Kelas',
            'Jurusan (Major)',
        ];
    }

    public function map($class): array
    {
        return [
            $class->id,
            $class->level,
            $class->name,
            $class->major,
        ];
    }
}
