<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pengeluaran</title>
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
    <h2 class="text-center">LAPORAN PENGELUARAN (KAS KELUAR)</h2>
    <p class="text-center">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Kategori</th>
                <th>Deskripsi</th>
                <th>Diajukan Oleh</th>
                <th>Nominal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expenses as $index => $e)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($e->date)->format('d M Y') }}</td>
                    <td>{{ $e->category->name ?? '-' }}</td>
                    <td>{{ $e->description }}</td>
                    <td>{{ $e->user->name ?? '-' }}</td>
                    <td class="text-end">{{ number_format($e->amount, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" class="text-end">TOTAL PENGELUARAN</th>
                <th class="text-end">{{ number_format($total, 0, ',', '.') }}</th>
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
