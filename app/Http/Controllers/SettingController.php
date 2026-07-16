<?php

namespace App\Http\Controllers;

use App\Models\SchoolSetting;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\SchoolClass;
use App\Models\FinancePost;
use App\Models\Bill;
use App\Models\BillDetail;
use App\Models\Payment;
use App\Models\PaymentDetail;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = SchoolSetting::pluck('value', 'key')->all();
        $academicYears = AcademicYear::latest()->get();

        return view('settings.index', compact('settings', 'academicYears'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', 'school_logo', 'school_stamp', 'app_logo']);

        foreach ($data as $key => $value) {
            SchoolSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        $fileKeys = ['school_logo', 'school_stamp', 'app_logo'];
        foreach ($fileKeys as $fileKey) {
            if ($request->hasFile($fileKey)) {
                $request->validate([
                    $fileKey => 'image|mimes:jpeg,png,jpg|max:2048'
                ]);

                $oldPath = SchoolSetting::get($fileKey);
                if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }

                $path = $request->file($fileKey)->store('settings', 'public');
                
                SchoolSetting::updateOrCreate(
                    ['key' => $fileKey],
                    ['value' => $path]
                );
            }
        }

        return redirect()->route('settings.index')->with('success', 'Pengaturan berhasil diperbarui.');
    }

    public function resetData(Request $request)
    {
        $request->validate([
            'password' => 'required|string'
        ]);

        if (!Hash::check($request->password, auth()->user()->password)) {
            return back()->with('error', 'Password salah! Penghapusan dibatalkan.');
        }

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            if ($request->reset_students) {
                Student::truncate();
                StudentClass::truncate();
            }
            if ($request->reset_classes) {
                SchoolClass::truncate();
            }
            if ($request->reset_academic_years) {
                AcademicYear::truncate();
            }
            if ($request->reset_finance_posts) {
                FinancePost::truncate();
            }
            if ($request->reset_bills) {
                Bill::truncate();
                BillDetail::truncate();
            }
            if ($request->reset_payments) {
                Payment::truncate();
                PaymentDetail::truncate();
            }
            if ($request->reset_expenses) {
                Expense::truncate();
                ExpenseCategory::truncate();
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return back()->with('success', 'Data yang dipilih berhasil dikosongkan!');
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            return back()->with('error', 'Terjadi kesalahan sistem saat menghapus data: ' . $e->getMessage());
        }
    }
}
