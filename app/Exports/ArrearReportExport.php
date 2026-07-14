<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ArrearReportExport implements FromView, ShouldAutoSize
{
    public $arrears, $selYear, $selClass, $totalArrears;

    public function __construct($arrears, $selYear, $selClass, $totalArrears)
    {
        $this->arrears = $arrears;
        $this->selYear = $selYear;
        $this->selClass = $selClass;
        $this->totalArrears = $totalArrears;
    }

    public function view(): View
    {
        return view('reports.print.arrear', [
            'arrears' => $this->arrears,
            'selYear' => $this->selYear,
            'selClass' => $this->selClass,
            'totalArrears' => $this->totalArrears,
            'action' => 'excel'
        ]);
    }
}
