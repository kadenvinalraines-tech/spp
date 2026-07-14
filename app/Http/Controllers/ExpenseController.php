<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Http\Requests\StoreExpenseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function index()
    {
        $activeYearId = \App\Models\AcademicYear::getActiveId();
        $expenses = Expense::with(['category', 'user', 'approver', 'academicYear'])
                    ->where('academic_year_id', $activeYearId)
                    ->latest()
                    ->paginate(10);
        return view('expenses.index', compact('expenses'));
    }

    public function create()
    {
        $categories = ExpenseCategory::orderBy('name')->get();
        return view('expenses.create', compact('categories'));
    }

    public function store(StoreExpenseRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['status'] = 'pending';

        if ($request->hasFile('receipt')) {
            $path = $request->file('receipt')->store('receipts', 'public');
            $data['receipt_path'] = $path;
        }

        $activeYearId = \App\Models\AcademicYear::getActiveId();
        $data['academic_year_id'] = $activeYearId;

        Expense::create($data);

        return redirect()->route('expenses.index')->with('success', 'Pengajuan pengeluaran berhasil disimpan dan menunggu persetujuan.');
    }

    public function show(Expense $expense)
    {
        $expense->load(['category', 'user', 'approver']);
        return view('expenses.show', compact('expense'));
    }

    public function approve(Request $request, Expense $expense)
    {
        $expense->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'rejection_reason' => null
        ]);

        return back()->with('success', 'Pengeluaran telah disetujui.');
    }

    public function reject(Request $request, Expense $expense)
    {
        $request->validate(['rejection_reason' => 'required|string']);
        
        $expense->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'rejection_reason' => $request->rejection_reason
        ]);

        return back()->with('success', 'Pengeluaran ditolak.');
    }
}
