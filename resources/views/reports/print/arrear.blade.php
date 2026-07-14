<!DOCTYPE html>
<html>
<head>
    <title>Laporan Tunggakan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 5px; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body onload="{{ $action === 'print' ? 'window.print()' : '' }}">
    <h2 class="text-center">LAPORAN TUNGGAKAN SISWA</h2>
    <p class="text-center">Tahun Pelajaran: {{ $selYear }} | Kelas: {{ $selClass }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Siswa</th>
                <th>NIS</th>
                <th>Kelas</th>
                <th>Tagihan</th>
                <th>Total Nominal</th>
                <th>Kurang Bayar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($arrears as $index => $ar)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $ar->student->name ?? '-' }}</td>
                    <td>{{ $ar->student->nis ?? '-' }}</td>
                    <td>{{ $ar->student->status == 'graduated' ? 'Alumni (' . $ar->student->latest_academic_year_name . ')' : ($ar->student->schoolClass->name ?? '-') }}</td>
                    <td>{{ $ar->financePost->name ?? '-' }} ({{ $ar->academicYear->name ?? '-' }})</td>
                    <td class="text-end">{{ number_format($ar->total_amount, 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($ar->total_amount - $ar->total_paid, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6" class="text-end">TOTAL TUNGGAKAN</th>
                <th class="text-end">{{ number_format($totalArrears, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    <table style="border: none; margin-top: 30px; width: 100%;">
        <tr>
            <td style="border: none; width: 70%;"></td>
            <td style="border: none; width: 30%; text-align: center;">
                <p>Petugas,</p>
                <div style="height: 100px; position: relative; margin: 0 auto; width: 200px;">
                    @if(isset($settings['school_stamp']))
                        <img src="{{ isset($action) && $action === 'pdf' ? public_path('storage/' . $settings['school_stamp']) : asset('storage/' . $settings['school_stamp']) }}" style="position: absolute; left: 10px; top: 10px; width: 80px; height: auto; opacity: 0.8; z-index: 1;" alt="Stempel">
                    @endif
                    @if(auth()->user() && auth()->user()->signature)
                        <img src="{{ isset($action) && $action === 'pdf' ? public_path('storage/' . auth()->user()->signature) : asset('storage/' . auth()->user()->signature) }}" style="position: absolute; left: 40px; top: 5px; width: 120px; height: auto; z-index: 2;" alt="Tanda Tangan">
                    @endif
                </div>
                <p><strong>( {{ auth()->user()->name ?? 'Administrator' }} )</strong></p>
            </td>
        </tr>
    </table>
</body>
</html>
