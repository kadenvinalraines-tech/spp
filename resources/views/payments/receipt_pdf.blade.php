<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kwitansi Pembayaran - {{ $payment->transaction_number }}</title>
    <style>
        @page {
            margin: 40px;
        }
        body { 
            font-family: Arial, Helvetica, sans-serif; 
            font-size: 11pt; 
            color: #000;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .kwitansi-container {
            border: 2px solid #000;
            padding: 20px 30px;
            position: relative;
            z-index: 1;
        }
        
        .watermark-bg {
            position: absolute;
            top: 25%;
            left: 20%;
            width: 60%;
            opacity: 0.08;
            z-index: -1;
            text-align: center;
        }
        .watermark-bg img {
            width: 100%;
            max-width: 350px;
        }
        
        /* Kop Surat */
        .kop-table {
            width: 100%;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .kop-logo {
            width: 12%;
            vertical-align: middle;
            text-align: left;
        }
        .kop-logo img {
            max-width: 80px;
            max-height: 80px;
        }
        .kop-text {
            width: 60%;
            vertical-align: middle;
        }
        .kop-text h2 {
            margin: 0;
            padding: 0;
            font-size: 14pt;
            font-weight: bold;
            color: #000;
            text-transform: uppercase;
        }
        .kop-text h1 {
            margin: 0;
            padding: 0;
            font-size: 16pt;
            font-weight: bold;
            color: #d32f2f; /* Dark red for school name like the example */
            text-transform: uppercase;
        }
        .kop-text p {
            margin: 2px 0 0 0;
            font-size: 9pt;
            line-height: 1.2;
        }
        
        .no-kwitansi-wrapper {
            width: 28%;
            vertical-align: top;
            text-align: right;
        }
        .no-kwitansi {
            display: inline-block;
            font-size: 12pt;
            font-style: italic;
            font-family: "Times New Roman", Times, serif;
            margin-right: 5px;
        }
        .no-box {
            display: inline-block;
            border: 1px solid #000;
            padding: 5px 10px;
            min-width: 120px;
            text-align: center;
            font-family: Arial, Helvetica, sans-serif;
            font-style: normal;
            font-size: 11pt;
            font-weight: bold;
        }

        /* Title */
        .kwitansi-title {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 25px;
        }
        .kwitansi-title h2 {
            margin: 0;
            padding: 0;
            font-size: 24pt;
            font-style: italic;
            font-family: "Times New Roman", Times, serif;
            letter-spacing: 3px;
        }

        /* Isi Kwitansi */
        .content-table {
            width: 100%;
            border-collapse: collapse;
        }
        .content-table td {
            padding: 8px 0;
            vertical-align: top;
        }
        .label-col {
            width: 22%;
            font-size: 11pt;
        }
        .colon-col {
            width: 3%;
            text-align: center;
        }
        .value-col {
            width: 75%;
        }
        
        /* Dotted Lines */
        .dotted-line {
            border-bottom: 1px dotted #000;
            display: inline-block;
            width: 100%;
            padding-bottom: 2px;
            font-size: 11pt;
        }

        .terbilang-text {
            font-style: italic;
            font-weight: bold;
            background-color: #f9f9f9;
        }

        .amount-box {
            display: inline-block;
            background-color: #e0e0e0;
            background-image: repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,0.5) 10px, rgba(255,255,255,0.5) 20px);
            border: 1px solid #999;
            padding: 5px 30px;
            font-size: 14pt;
            font-weight: bold;
        }

        /* Uraian / Guna Membayar */
        .uraian-list {
            margin: 0;
            padding: 0 0 0 15px;
        }
        .uraian-list li {
            margin-bottom: 3px;
        }

        /* Footer / Tanda Tangan */
        .footer-table {
            width: 100%;
            margin-top: 40px;
        }
        .footer-table td {
            vertical-align: bottom;
        }
        .signature-box {
            text-align: center;
            width: 250px;
            display: inline-block;
        }
        .signature-area {
            height: 90px;
            position: relative;
            margin: 5px auto;
        }
        .stamp-img {
            position: absolute;
            left: 10px;
            top: 0;
            width: 80px;
            height: auto;
            opacity: 0.8;
            z-index: 1;
        }
        .sign-img {
            position: absolute;
            left: 50px;
            top: -10px;
            width: 120px;
            height: auto;
            z-index: 2;
        }
        .name-line {
            border-bottom: 1px dotted #000;
            display: inline-block;
            min-width: 150px;
            font-weight: bold;
            padding-bottom: 2px;
        }
    </style>
</head>
<body>
    @php
        $schoolName = \App\Models\SchoolSetting::get('school_name', 'NAMA SEKOLAH DEMO');
        $schoolAddress = \App\Models\SchoolSetting::get('school_address', 'Jl. Pendidikan No. 1, Kota Demo');
        $appLogo = \App\Models\SchoolSetting::get('app_logo');
        $petugasName = $payment->user->name ?? 'Petugas TU';
        
        // Terbilang Function
        $terbilangHelper = function($angka) use (&$terbilangHelper) {
            $angka = abs($angka);
            $baca = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
            $terbilang = "";
            if ($angka < 12) {
                $terbilang = " " . $baca[$angka];
            } else if ($angka < 20) {
                $terbilang = $terbilangHelper($angka - 10) . " belas";
            } else if ($angka < 100) {
                $terbilang = $terbilangHelper($angka / 10) . " puluh" . $terbilangHelper($angka % 10);
            } else if ($angka < 200) {
                $terbilang = " seratus" . $terbilangHelper($angka - 100);
            } else if ($angka < 1000) {
                $terbilang = $terbilangHelper($angka / 100) . " ratus" . $terbilangHelper($angka % 100);
            } else if ($angka < 2000) {
                $terbilang = " seribu" . $terbilangHelper($angka - 1000);
            } else if ($angka < 1000000) {
                $terbilang = $terbilangHelper($angka / 1000) . " ribu" . $terbilangHelper($angka % 1000);
            } else if ($angka < 1000000000) {
                $terbilang = $terbilangHelper($angka / 1000000) . " juta" . $terbilangHelper($angka % 1000000);
            }
            return $terbilang;
        };

        if (class_exists('NumberFormatter')) {
            $f = new NumberFormatter("id", NumberFormatter::SPELLOUT);
            $hurufTerbilang = ucwords($f->format($payment->total_amount)) . " Rupiah";
        } else {
            $hurufTerbilang = ucwords(trim($terbilangHelper($payment->total_amount))) . " Rupiah";
        }

        $city = \App\Models\SchoolSetting::get('school_city', 'Bogor');
    @endphp

    <div class="kwitansi-container">
        <!-- WATERMARK -->
        @if($appLogo)
            <div class="watermark-bg">
                <img src="{{ public_path('storage/' . $appLogo) }}" alt="Watermark">
            </div>
        @endif
        
        <!-- KOP SURAT & NO KWITANSI -->
        <table class="kop-table">
            <tr>
                <td class="kop-logo">
                    @if($appLogo)
                        <img src="{{ public_path('storage/' . $appLogo) }}" alt="Logo">
                    @endif
                </td>
                <td class="kop-text">
                    <h2>YAYASAN SIRUNG ASHA INDONESIA</h2>
                    <h1>{{ $schoolName }}</h1>
                    <p>{{ $schoolAddress }}</p>
                </td>
                <td class="no-kwitansi-wrapper">
                    <span class="no-kwitansi">No.</span> 
                    <span class="no-box">{{ $payment->transaction_number }}</span>
                </td>
            </tr>
        </table>

        <!-- TITLE -->
        <div class="kwitansi-title">
            <h2>KWITANSI</h2>
        </div>

        <!-- ISI KWITANSI -->
        <table class="content-table">
            <tr>
                <td class="label-col">Sudah Terima dari</td>
                <td class="colon-col">:</td>
                <td class="value-col">
                    <div class="dotted-line">
                        <strong>{{ $payment->student->name }}</strong> 
                        (Kelas: {{ $payment->student->status == 'graduated' ? 'Alumni' : ($payment->student->schoolClass->name ?? '-') }})
                    </div>
                </td>
            </tr>
            <tr>
                <td class="label-col">Uang Sebanyak</td>
                <td class="colon-col">:</td>
                <td class="value-col">
                    <div class="amount-box">
                        Rp. {{ number_format($payment->total_amount, 0, ',', '.') }}
                    </div>
                </td>
            </tr>
            <tr>
                <td class="label-col">Terbilang</td>
                <td class="colon-col">:</td>
                <td class="value-col">
                    <div class="dotted-line terbilang-text">
                        # {{ $hurufTerbilang }} #
                    </div>
                </td>
            </tr>
            <tr>
                <td class="label-col">Guna Membayar</td>
                <td class="colon-col">:</td>
                <td class="value-col">
                    <div class="dotted-line" style="border-bottom: none;">
                        <ul class="uraian-list">
                            @foreach($payment->details as $detail)
                                @php
                                    $billName = $detail->billDetail->bill->financePost->name ?? 'Tagihan';
                                    $month = $detail->billDetail->month ? ' Bulan ' . $detail->billDetail->month : '';
                                    $amount = number_format($detail->amount_paid, 0, ',', '.');
                                @endphp
                                <li>{{ $billName }}{{ $month }} — Rp. {{ $amount }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="dotted-line" style="margin-top: 5px;"></div>
                    <div class="dotted-line" style="margin-top: 15px;"></div>
                </td>
            </tr>
        </table>

        <!-- FOOTER & TTD -->
        <table class="footer-table">
            <tr>
                <td style="width: 50%;">
                    <!-- Kosong / Bisa diisi catatan -->
                    <p style="font-size: 9pt; color: #555; margin: 0;">
                        * Dicetak dari Sistem Keuangan Sekolah<br>
                        * Metode Pembayaran: {{ $payment->payment_method }}
                    </p>
                </td>
                <td style="width: 50%; text-align: right;">
                    <div class="signature-box">
                        <div style="margin-bottom: 10px;">
                            {{ ucwords(strtolower(trim($city))) }}, {{ \Carbon\Carbon::parse($payment->date)->locale('id')->isoFormat('D MMMM Y') }}<br>
                            Penerima,
                        </div>
                        
                        <div class="signature-area">
                            @if(isset($settings['school_stamp']) && $settings['school_stamp'])
                                <img src="{{ public_path('storage/' . $settings['school_stamp']) }}" class="stamp-img" alt="Stempel">
                            @endif
                            @if($payment->user && $payment->user->signature)
                                <img src="{{ public_path('storage/' . $payment->user->signature) }}" class="sign-img" alt="Tanda Tangan">
                            @endif
                        </div>
                        
                        <div class="name-line">
                            {{ $petugasName }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>

    </div>
</body>
</html>
