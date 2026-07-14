<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CashReportExport implements FromView, ShouldAutoSize
{
    public $transactions, $startDate, $endDate, $totalIncome, $totalExpense, $balance;

    public function __construct($transactions, $startDate, $endDate, $totalIncome, $totalExpense, $balance)
    {
        $this->transactions = $transactions;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->totalIncome = $totalIncome;
        $this->totalExpense = $totalExpense;
        $this->balance = $balance;
    }

    public function view(): View
    {
        return view('reports.print.cash', [
            'transactions' => $this->transactions,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'totalIncome' => $this->totalIncome,
            'totalExpense' => $this->totalExpense,
            'balance' => $this->balance,
            'action' => 'excel'
        ]);
    }
}
