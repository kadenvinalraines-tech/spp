<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AcademicYearSessionController extends Controller
{
    public function setSession(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        session(['active_academic_year_id' => $request->academic_year_id]);

        return back()->with('success', 'Tahun Ajaran aktif berhasil diubah.');
    }
}
