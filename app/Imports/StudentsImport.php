<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\AcademicYear;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StudentsImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    private $classes;

    public function __construct()
    {
        // Preload classes to avoid querying in loop
        $this->classes = SchoolClass::all()->keyBy(function ($item) {
            return strtolower($item->name);
        });

        if ($this->classes->isEmpty()) {
            throw new \Exception('Data Kelas di sistem masih kosong. Anda harus membuat Data Kelas terlebih dahulu sebelum meng-import siswa.');
        }
    }

    public function collection(Collection $rows)
    {
        $activeYearId = AcademicYear::getActiveId();
        
        if (!$activeYearId) {
            throw new \Exception('Tahun ajaran aktif belum diatur di pengaturan sistem. Harap set tahun ajaran aktif terlebih dahulu.');
        }

        DB::beginTransaction();
        try {
            foreach ($rows as $row) {
                if (empty($row['nis']) || empty($row['nama_siswa']) || empty($row['kelas'])) {
                    continue; // Skip invalid row
                }

                $className = strtolower($row['kelas']);
                $class = $this->classes->get($className);
                
                if (!$class) {
                    continue; // Skip jika kelas tidak ditemukan
                }

                $gender = strtoupper($row['jenis_kelamin'] ?? 'L');
                if (!in_array($gender, ['L', 'P'])) {
                    $gender = 'L'; // Default
                }

                $student = Student::updateOrCreate(
                    ['nis' => $row['nis']],
                    [
                        'nisn'     => $row['nisn'] ?? null,
                        'name'     => $row['nama_siswa'],
                        'gender'   => $gender,
                        'address'  => $row['alamat'] ?? null,
                        'phone'    => $row['no_telp'] ?? null,
                        'status'   => !empty($row['status']) ? strtolower($row['status']) : 'active',
                        'class_id' => $class->id, // update class_id in students table too if there is one
                    ]
                );

                $student->studentClasses()->updateOrCreate(
                    ['academic_year_id' => $activeYearId],
                    ['class_id' => $class->id]
                );
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function chunkSize(): int
    {
        return 200; // Adjust chunk size as needed
    }
}
