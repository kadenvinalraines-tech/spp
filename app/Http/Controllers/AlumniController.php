<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AlumniController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $academic_year_id = $request->get('academic_year_id');
        
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();

        $query = Student::with(['studentClasses.schoolClass', 'studentClasses.academicYear', 'bills'])
            ->where('status', 'graduated');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        if ($academic_year_id) {
            $query->whereHas('studentClasses', function ($q) use ($academic_year_id) {
                $q->where('academic_year_id', $academic_year_id);
            });
        }

        $alumnis = $query->latest()->paginate(10)->withQueryString();

        // Calculate arrears for each alumni
        $alumnis->getCollection()->transform(function($alumni) {
            $alumni->tunggakan = $alumni->bills->sum('total_amount') - $alumni->bills->sum('total_paid');
            return $alumni;
        });

        return view('alumni.index', compact('alumnis', 'academicYears', 'academic_year_id', 'search'));
    }

    public function show($id)
    {
        $alumni = Student::with(['studentClasses.schoolClass', 'bills.financePost', 'bills.academicYear', 'bills.details'])->findOrFail($id);
        
        $unpaidBills = collect();
        if ($alumni->bills) {
            $unpaidBills = $alumni->bills->filter(function($b) {
                return ($b->total_amount - $b->total_paid) > 0;
            });
        }

        return view('alumni.show', compact('alumni', 'unpaidBills'));
    }

    public function edit($id)
    {
        $student = Student::findOrFail($id);
        $classes = \App\Models\SchoolClass::orderBy('level')->orderBy('name')->get();

        return view('alumni.edit', compact('student', 'classes'));
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        
        $data = $request->validate([
            'nis' => ['required', 'string', 'max:20', \Illuminate\Validation\Rule::unique('students', 'nis')->ignore($student->id)],
            'nisn' => ['nullable', 'string', 'max:20', \Illuminate\Validation\Rule::unique('students', 'nisn')->ignore($student->id)],
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:L,P'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'in:active,graduated,dropout'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048']
        ]);

        if ($request->hasFile('photo')) {
            if ($student->photo && Storage::disk('public')->exists($student->photo)) {
                Storage::disk('public')->delete($student->photo);
            }
            $path = $request->file('photo')->store('students', 'public');
            $data['photo'] = $path;
        }

        $student->update($data);

        return redirect()->route('alumni.index')->with('success', 'Data alumni berhasil diperbarui.');
    }

    public function bulkDestroy()
    {
        $alumnis = Student::with('bills')->where('status', 'graduated')->get();
        $deletedCount = 0;
        
        foreach ($alumnis as $alumni) {
            $tunggakan = $alumni->bills->sum('total_amount') - $alumni->bills->sum('total_paid');
            if ($tunggakan <= 0) {
                if ($alumni->photo && Storage::disk('public')->exists($alumni->photo)) {
                    Storage::disk('public')->delete($alumni->photo);
                }
                $alumni->delete();
                $deletedCount++;
            }
        }

        if ($deletedCount > 0) {
            return redirect()->route('alumni.index')->with('success', "Berhasil menghapus secara permanen {$deletedCount} data alumni yang sudah lunas.");
        } else {
            return redirect()->route('alumni.index')->with('info', 'Tidak ada data alumni lunas yang bisa dihapus secara massal.');
        }
    }

    public function destroy($id)
    {
        $alumni = Student::with('bills')->findOrFail($id);
        
        $tunggakan = $alumni->bills->sum('total_amount') - $alumni->bills->sum('total_paid');

        if ($tunggakan > 0) {
            return redirect()->route('alumni.index')->with('error', 'Data alumni tidak dapat dihapus karena masih memiliki tunggakan.');
        }

        if ($alumni->photo && Storage::disk('public')->exists($alumni->photo)) {
            Storage::disk('public')->delete($alumni->photo);
        }
        $alumni->delete();

        return redirect()->route('alumni.index')->with('success', 'Data alumni berhasil dihapus.');
    }
    public function sendWa($id)
    {
        $alumni = Student::with('bills.financePost')->findOrFail($id);
        
        if (!$alumni->phone) {
            return redirect()->back()->with('error', 'Nomor WhatsApp alumni tidak tersedia.');
        }

        $sisaTunggakan = 0;
        $rincianArr = [];
        
        foreach ($alumni->bills as $b) {
            $sisa = $b->total_amount - $b->total_paid;
            if ($sisa > 0) {
                $sisaTunggakan += $sisa;
                $namaPos = $b->financePost->name ?? 'Tagihan';
                $rincianArr[] = "- " . $namaPos . ": Rp " . number_format($sisa, 0, ',', '.');
            }
        }

        if ($sisaTunggakan <= 0) {
            return redirect()->back()->with('error', 'Alumni ini tidak memiliki tunggakan.');
        }

        $rincianText = implode("\n", $rincianArr);
        $totalText = "Rp " . number_format($sisaTunggakan, 0, ',', '.');

        $text = "PEMBERITAHUAN TUNGGAKAN\n\nHalo, Wali dari Alumni *{$alumni->name}*\nKami menginformasikan bahwa terdapat sisa administrasi sekolah yang belum diselesaikan sebesar *{$totalText}*.\n\nRincian Tunggakan:\n{$rincianText}\n\nMohon untuk segera diselesaikan. Abaikan pesan ini jika sudah melakukan pembayaran. Terima kasih.";

        try {
            $delayMin = intval(\App\Models\SchoolSetting::get('wa_delay_min', 3));
            $delayMax = intval(\App\Models\SchoolSetting::get('wa_delay_max', 7));
            $gatewayUrl = env('WA_GATEWAY_URL', 'http://localhost:3000');
            
            \Illuminate\Support\Facades\Http::timeout(5)->post($gatewayUrl . '/send-bulk', [
                'messages' => [
                    [
                        'phone' => $alumni->phone,
                        'message' => $text
                    ]
                ],
                'delayMin' => $delayMin,
                'delayMax' => $delayMax,
            ]);

            return redirect()->back()->with('success', 'Pesan penagihan WhatsApp sedang dikirim.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal terhubung ke WA Gateway: ' . $e->getMessage());
        }
    }
}
