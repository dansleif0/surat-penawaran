<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak SPK - {{ $skp->offer->nama_klien }}</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* 1. RESET STANDAR UNTUK PRINT */
        @media print {
            @page {
                size: A4;
                margin: 0;
                /* Margin nol agar kita kontrol penuh lewat CSS */
            }

            body {
                margin: 0;
                padding: 0;
                background-color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .no-print {
                display: none !important;
            }

            /* KONTROL PRESISI A4 */
            #main-container {
                width: 210mm;
                margin: 0 auto !important;
                padding: 25mm 25mm 20mm 25mm !important; /* Margin standar surat resmi */
                box-shadow: none !important;
                border: none !important;
                float: none !important;
            }

            /* LOGIKA PRINT TANPA KOP */
            .hide-header-on-print .invoice-header {
                display: none !important;
            }

            .hide-header-on-print #main-container {
                padding-top: 60mm !important;
                /* Menambahkan ruang kosong setinggi kop surat fisik */
            }
        }

        /* 2. TAMPILAN LAYAR (PREVIEW) */
        body {
            background-color: #f3f4f6;
            /* Abu-abu netral */
        }

        #main-container {
            background-color: white;
            width: 210mm;
            min-height: 297mm;
            /* Simulasi kertas A4 di layar */
            margin: 40px auto;
            padding: 25mm;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        /* Font Resmi */
        body {
            font-family: 'Times New Roman', Times, serif;
            color: black;
            line-height: 1.5;
        }

        .sans {
            font-family: Arial, Helvetica, sans-serif;
        }

        .nav-floating {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 100;
            display: flex;
            gap: 10px;
        }
    </style>
</head>

<body class="bg-gray-100 text-black text-sm">

    {{-- Tombol Navigasi Terapung (Hanya Muncul di Layar) --}}
    <div class="nav-floating no-print">
        <button onclick="printWithHeader()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-lg flex items-center gap-2 transition">
            <span>🖨️</span> Cetak Normal
        </button>
        <button onclick="printWithoutHeader()" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded shadow-lg flex items-center gap-2 transition">
            <span>📄</span> Tanpa Kop Surat
        </button>
        <button onclick="window.close()" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded shadow-lg transition">
            Tutup
        </button>
    </div>

    <div id="main-container">
        {{-- HEADER KOP SURAT --}}
        <header class="w-full mb-6 invoice-header"> {{-- Tambahkan class invoice-header di sini --}}
            <div class="w-full">
                <img src="{{ asset('images/kopsurat.jpg') }}" alt="Kop Surat PT Tasniem Gerai Inspirasi" class="w-full h-auto">
            </div>
        </header>

        {{-- 2. JUDUL SURAT --}}
        <div class="text-center mb-6">
            <h2 class="text-lg font-bold underline uppercase tracking-wide">SURAT PERINTAH KERJA</h2>
        </div>

        {{-- LOGIKA TANGGAL & FORMAT NOMOR --}}
        @php
        use Carbon\Carbon;

        $namaHari = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
        $namaBulan = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];

        $formatIndo = function($date, $pakaiHari = false) use ($namaHari, $namaBulan) {
        if (!$date) return '-';
        $d = Carbon::parse($date);
        $hari = $namaHari[$d->format('l')];
        $tgl = $d->format('d');
        $bln = $namaBulan[(int)$d->format('m')];
        $thn = $d->format('Y');
        return $pakaiHari ? "$hari, $tgl $bln $thn" : "$tgl $bln $thn";
        };

        // Helper Bulan Romawi
        $bulanRomawi = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'];

        $bulanOffer = (int) $skp->offer->created_at->format('n');
        $tahunOffer = $skp->offer->created_at->format('Y');

        $nomorSuratLengkap = sprintf('SP-%04d/SP/TGI-1/%s/%s', $skp->offer_id, $bulanRomawi[$bulanOffer], $tahunOffer);
        @endphp

        {{-- 3. ISI SURAT --}}
        <div class="text-justify leading-relaxed text-gray-900 space-y-4">

            <p>
                Berdasarkan Surat Penawaran Kerja Nomor: <strong>{{ $nomorSuratLengkap }}</strong>
                tertanggal <strong>{{ $formatIndo($skp->offer->created_at) }}</strong>,
                tentang <strong>{{ $skp->judul_pekerjaan }}</strong>,
                maka pada hari ini, <strong>{{ $formatIndo($skp->tanggal_surat, true) }}</strong>,
                kami yang bertanda tangan di bawah ini :
            </p>

            <div class="pl-4">
                <table class="w-full">
                    <tr>
                        <td class="w-32 align-top">Nama</td>
                        <td class="w-4 align-top">:</td>
                        <td class="align-top font-bold">{{ $skp->pihak_satu_nama }}</td>
                    </tr>
                    @if($skp->pihak_satu_perusahaan)
                    <tr>
                        <td class="align-top">Perusahaan</td>
                        <td class="align-top">:</td>
                        <td class="align-top">{{ $skp->pihak_satu_perusahaan }}</td>
                    </tr>
                    @endif
                    @if($skp->pihak_satu_jabatan)
                    <tr>
                        <td class="align-top">Jabatan</td>
                        <td class="align-top">:</td>
                        <td class="align-top">{{ $skp->pihak_satu_jabatan }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="align-top">Alamat</td>
                        <td class="align-top">:</td>
                        <td class="align-top">{{ $skp->pihak_satu_alamat }}</td>
                    </tr>
                </table>
                <p class="mt-1 pl-36 text-sm">yang selanjutnya disebut <strong>PIHAK I (pemberi pekerjaan).</strong></p>
            </div>

            <div class="pl-4 mt-4">
                <table class="w-full">
                    <tr>
                        <td class="w-32 align-top">Nama</td>
                        <td class="w-4 align-top">:</td>
                        <td class="align-top font-bold">{{ $skp->pihak_dua_nama }}</td>
                    </tr>
                    @if($skp->pihak_dua_perusahaan)
                    <tr>
                        <td class="align-top">Perusahaan</td>
                        <td class="align-top">:</td>
                        <td class="align-top">{{ $skp->pihak_dua_perusahaan }}</td>
                    </tr>
                    @endif
                    @if($skp->pihak_dua_jabatan)
                    <tr>
                        <td class="align-top">Jabatan</td>
                        <td class="align-top">:</td>
                        <td class="align-top">{{ $skp->pihak_dua_jabatan }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="align-top">Alamat</td>
                        <td class="align-top">:</td>
                        <td class="align-top">{{ $skp->pihak_dua_alamat }}</td>
                    </tr>
                </table>
                <p class="mt-1 pl-36 text-sm">yang selanjutnya disebut <strong>PIHAK II (penerima pekerjaan).</strong></p>
            </div>

            <p class="mt-6">bahwa Pihak I dengan ini memberikan perintah kepada Pihak II untuk melaksanakan ;</p>

            <table class="w-full mt-2 ml-4">
                <tr>
                    <td class="w-40 align-top font-bold">Pekerjaan</td>
                    <td class="w-4 align-top">:</td>
                    <td class="align-top">{{ $skp->judul_pekerjaan }}</td>
                </tr>
                <tr>
                    <td class="align-top font-bold">Lokasi</td>
                    <td class="align-top">:</td>
                    <td class="align-top">{{ $skp->lokasi_pekerjaan }}</td>
                </tr>
                <tr>
                    <td class="align-top font-bold">Masa Pekerjaan</td>
                    <td class="align-top">:</td>
                    <td class="align-top">{{ $skp->durasi_hari }}</td>
                </tr>
                <tr>
                    <td class="font-bold">Terhitung</td>
                    <td class="align-top">:</td>
                    <td class="align-top">dari tanggal {{ $formatIndo($skp->tanggal_mulai) }} sampai dengan tanggal {{ $formatIndo($skp->tanggal_selesai) }}</td>
                </tr>
                <tr>
                    <td class="align-top font-bold">Total Nilai Pekerjaan</td>
                    <td class="align-top">:</td>
                    <td class="align-top font-bold">Rp {{ number_format($skp->nilai_pekerjaan, 0, ',', '.') }},-</td>
                </tr>

                {{-- Tampilkan Sistem Pembayaran HANYA jika ada data --}}
                @if($skp->termin_pembayaran && count($skp->termin_pembayaran) > 0)
                <tr>
                    <td class="align-top font-bold">Sistem Pembayaran</td>
                    <td class="align-top">:</td>
                    <td class="align-top">
                        <ul class="list-none pl-0 m-0">
                            @foreach($skp->termin_pembayaran as $termin)
                            <li class="mb-1">
                                {{ $termin['keterangan'] }}

                                {{-- Tanggal jika ada --}}
                                @if(!empty($termin['tanggal']) && $termin['tanggal'] != '-')
                                ({{ $formatIndo($termin['tanggal']) }})
                                @endif

                                {{-- Perubahan di sini: Format Rupiah untuk Amount --}}
                                , dibayarkan sebesar <strong>Rp {{ number_format((float)$termin['jumlah'], 0, ',', '.') }},-</strong>
                            </li>
                            @endforeach
                        </ul>
                    </td>
                </tr>
                @endif
            </table>

            <div class="mt-6">
                <p class="font-bold underline mb-2">Syarat, ketentuan dan keterangan pelaksanaan pekerjaan :</p>
                <ol class="list-decimal list-outside ml-5 space-y-1 text-justify">
                    <li>Item pekerjaan, bobot prosentase pekerjaan, volume pekerjaan, gambar kerja dan Rencana Kerja dan Syarat – Syarat (RKS) merupakan bagian yang tidak terpisahkan dari Surat Perintah Kerja (SPK) ini.</li>
                    <li>Apabila dalam masa pelaksanaan pekerjaan ada perubahan – perubahan secara teknis, maka akan diatur dan dituangkan dalam bentuk SPK Addendum, yang akan diberitahukan oleh Pihak I.</li>
                </ol>
            </div>

            <p class="mt-6">Demikian Surat Perintah Kerja ini disusun untuk dipergunakan sebagaimana mestinya.</p>
        </div>

        {{-- 4. TANDA TANGAN --}}
        <div class="mt-16 flex justify-between text-center px-8">
            <div class="w-5/12">
                <p class="mb-24 font-bold">PIHAK I<br><span class="font-normal">Pemberi Pekerjaan</span></p>
                <p class="font-bold underline uppercase">{{ $skp->pihak_satu_nama }}</p>
            </div>
            <div class="w-5/12">
                <p class="mb-24 font-bold">PIHAK II<br><span class="font-normal">Penerima Pekerjaan</span></p>
                <p class="font-bold underline uppercase">{{ $skp->pihak_dua_nama }}</p>
            </div>
        </div>

    </div>

    <script>
        // Fungsi Cetak Normal
        function printWithHeader() {
            document.body.classList.remove('hide-header-on-print');
            window.print();
        }

        // Fungsi Cetak Tanpa Kop
        function printWithoutHeader() {
            document.body.classList.add('hide-header-on-print');
            window.print();
        }
    </script>
</body>

</html>