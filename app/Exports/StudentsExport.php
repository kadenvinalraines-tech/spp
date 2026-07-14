<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StudentsExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $search;
    protected $class_id;

    public function __construct($search = null, $class_id = null)
    {
        $this->search = $search;
        $this->class_id = $class_id;
    }

    public function query()
    {
        $query = Student::query()->with('schoolClass');

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('nis', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->class_id) {
            $query->where('class_id', $this->class_id);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'NIS',
            'NISN',
            'Nama Siswa',
            'Jenis Kelamin',
            'Kelas',
            'Alamat',
            'No. Telp',
            'Status',
        ];
    }

    public function map($student): array
    {
        return [
            $student->nis,
            $student->nisn,
            $student->name,
            $student->gender === 'L' ? 'Laki-laki' : 'Perempuan',
            $student->schoolClass ? $student->schoolClass->name : '-',
            $student->address,
            $student->phone,
            ucfirst($student->status),
        ];
    }
}
