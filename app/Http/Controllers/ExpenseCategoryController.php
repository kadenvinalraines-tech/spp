<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use App\Http\Requests\StoreExpenseCategoryRequest;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        $categories = ExpenseCategory::latest()->paginate(10);
        return view('expense-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('expense-categories.create');
    }

    public function store(StoreExpenseCategoryRequest $request)
    {
        ExpenseCategory::create($request->validated());
        return redirect()->route('expense-categories.index')->with('success', 'Kategori Pengeluaran berhasil ditambahkan.');
    }

    public function edit(ExpenseCategory $expense_category)
    {
        return view('expense-categories.edit', compact('expense_category'));
    }

    public function update(StoreExpenseCategoryRequest $request, ExpenseCategory $expense_category)
    {
        $expense_category->update($request->validated());
        return redirect()->route('expense-categories.index')->with('success', 'Kategori Pengeluaran berhasil diperbarui.');
    }

    public function destroy(ExpenseCategory $expense_category)
    {
        $expense_category->delete();
        return redirect()->route('expense-categories.index')->with('success', 'Kategori Pengeluaran berhasil dihapus.');
    }
}
