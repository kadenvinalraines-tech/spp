<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PaymentReportExport implements FromView, ShouldAutoSize
{
    public $payments, $startDate, $endDate, $total;

    public function __construct($payments, $startDate, $endDate, $total)
    {
        $this->payments = $payments;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->total = $total;
    }

    public function view(): View
    {
        return view('reports.print.payment', [
            'payments' => $this->payments,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'total' => $this->total,
            'action' => 'excel'
        ]);
    }
}
