<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Tagihan Siswa</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; border-bottom: 2px solid #000; margin-bottom: 20px; padding-bottom: 10px; }
        .header h2 { margin: 0; font-size: 18px; }
        .header p { margin: 5px 0 0 0; }
        .title { text-align: center; font-size: 16px; font-weight: bold; margin-bottom: 15px; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table, th, td { border: 1px solid #333; }
        th, td { padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-danger { color: #dc3545; }
        .text-success { color: #198754; }
        ul { margin: 0; padding-left: 15px; }
    </style>
</head>
<body>

    <div class="header">
        <h2>{{ $settings['school_name'] ?? 'SEKOLAH DEMO' }}</h2>
        <p>{{ $settings['school_address'] ?? 'Alamat Sekolah' }} | Telp: {{ $settings['school_phone'] ?? '-' }}</p>
    </div>

    <div class="title">
        Laporan Daftar Tagihan Siswa
        @if(request('class_id'))
            @php $kelas = \App\Models\SchoolClass::find(request('class_id')); @endphp
            <br><small>Kelas: {{ $kelas ? $kelas->name : 'Semua' }}</small>
        @endif
        @if(request('finance_post_id'))
            @php $pos = \App\Models\FinancePost::find(request('finance_post_id')); @endphp
            <br><small>Pos Tagihan: {{ $pos ? $pos->name : 'Semua' }}</small>
        @endif
    </div>

    @php
        $grandTotalKeseluruhan = 0;
    @endphp

    @forelse($groupedStudents as $className => $students)
        <h3 style="margin-top: 20px; margin-bottom: 10px; font-size: 14px; text-transform: uppercase;">KELAS: {{ $className }}</h3>
        <table>
            <thead>
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th width="30%">Nama Siswa & NIS</th>
                    <th width="40%">Rincian Tagihan Belum Lunas</th>
                    <th width="15%">Total Tunggakan</th>
                    <th width="10%" class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @php $subTotal = 0; @endphp
                @foreach($students as $index => $student)
                    @php
                        $dueUnpaidDetails = collect();
                        foreach($student->bills as $bill) {
                            if($bill->status !== 'paid') {
                                foreach($bill->details as $detail) {
                                    if($detail->status !== 'paid' && $detail->isDue()) {
                                        $dueUnpaidDetails->push($detail);
                                    }
                                }
                            }
                        }
                        $totalTunggakan = $dueUnpaidDetails->sum(function($detail) {
                            return $detail->amount - $detail->paid_amount;
                        });
                        $subTotal += $totalTunggakan;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $student->name }}</strong><br>
                            <small>NIS: {{ $student->nis ?? '-' }}</small>
                        </td>
                        <td>
                            @if($dueUnpaidDetails->isEmpty())
                                <span style="color: #198754;">Tidak ada tunggakan</span>
                            @else
                                <ul style="color: #dc3545; font-size: 11px;">
                                    @foreach($dueUnpaidDetails as $detail)
                                        <li>
                                            {{ $detail->bill->financePost->name ?? 'Tagihan' }}
                                            @if($detail->month) (Bulan {{ $detail->month }}) @endif : 
                                            Rp {{ number_format($detail->amount - $detail->paid_amount, 0, ',', '.') }}
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </td>
                        <td class="text-right">
                            <strong>Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</strong>
                        </td>
                        <td class="text-center">
                            @if($totalTunggakan == 0)
                                <strong style="color: #198754;">Lunas</strong>
                            @else
                                <strong style="color: #dc3545;">Tunggakan</strong>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-right">TOTAL TUNGGAKAN KELAS {{ strtoupper($className) }}:</th>
                    <th class="text-right">Rp {{ number_format($subTotal, 0, ',', '.') }}</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
        @php $grandTotalKeseluruhan += $subTotal; @endphp
    @empty
        <p class="text-center">Tidak ada data tagihan.</p>
    @endforelse

    @if($groupedStudents->count() > 0)
    <div style="margin-top: 20px; padding: 10px; background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24;">
        <h3 style="margin: 0; text-align: right; font-size: 16px;">TOTAL KESELURUHAN SEMUA KELAS: Rp {{ number_format($grandTotalKeseluruhan, 0, ',', '.') }}</h3>
    </div>
    @endif

    <div style="margin-top: 30px; text-align: right;">
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
        <br><br><br>
        <p><strong>Bagian Keuangan</strong></p>
    </div>

</body>
</html>
