<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ExpenseReportExport implements FromView, ShouldAutoSize
{
    public $expenses, $startDate, $endDate, $total;

    public function __construct($expenses, $startDate, $endDate, $total)
    {
        $this->expenses = $expenses;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->total = $total;
    }

    public function view(): View
    {
        return view('reports.print.expense', [
            'expenses' => $this->expenses,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'total' => $this->total,
            'action' => 'excel'
        ]);
    }
}
