<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak BAST - {{ $bast->no_surat }}</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* Styling Khusus Print */
        @media print {
            @page {
                size: A4;
                margin: 2.5cm;
                /* Margin surat resmi biasanya agak lebar */
            }

            body {
                background-color: white;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            /* Paksa Halaman Baru */
            .page-break {
                page-break-before: always;
            }

            /* Hindari pemotongan elemen di tengah */
            .avoid-break {
                page-break-inside: avoid;
            }

            /* Hapus elemen no-print */
            .no-print {
                display: none !important;
            }
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
    </style>
</head>

<body class="bg-white text-black">

    <div class="max-w-[21cm] mx-auto">

        {{-- HEADER KOP SURAT --}}
        <header class="w-full mb-6 invoice-header"> {{-- Tambahkan class invoice-header di sini --}}
            <div class="w-full">
                <img src="{{ asset('images/kopsurat.jpg') }}" alt="Kop Surat PT Tasniem Gerai Inspirasi" class="w-full h-auto">
            </div>
        </header>

        {{-- 2. JUDUL DOKUMEN --}}
        <div class="text-center mb-8">
            <h2 class="text-lg font-bold underline uppercase">BERITA ACARA SERAH TERIMA PEKERJAAN</h2>
            <p class="font-bold text-md mt-1">No. {{ $bast->no_surat }}</p>
        </div>

        {{-- PHP LOGIC TANGGAL INDONESIA --}}
        @php
        use Carbon\Carbon;
        $date = Carbon::parse($bast->tanggal_bast);
        $days = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
        $months = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];

        $hari = $days[$date->format('l')];
        $tanggal = $date->format('d');
        $bulan = $months[(int)$date->format('m')];
        $tahun = $date->format('Y');
        @endphp

        {{-- 3. ISI SURAT --}}
        <div class="text-justify text-md space-y-4">
            <p>
                Pada hari ini <strong>{{ $hari }}</strong> tanggal <strong>{{ $tanggal }}</strong> bulan <strong>{{ $bulan }}</strong> tahun <strong>{{ $tahun }}</strong> telah dilakukan serah terima hasil pekerjaan oleh dan diantara:
            </p>

            <div class="pl-4">
                <table class="w-full">
                    <tr>
                        <td class="w-6 align-top">1.</td>
                        <td class="w-40 align-top">Nama</td>
                        <td class="w-4 align-top">:</td>
                        <td class="align-top font-bold">{{ $bast->pihak_pertama_nama }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="align-top">Jabatan</td>
                        <td class="align-top">:</td>
                        <td class="align-top">{{ $bast->pihak_pertama_jabatan }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="align-top">Perusahaan</td>
                        <td class="align-top">:</td>
                        <td class="align-top">PT. Tasniem Gerai Inspirasi</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="align-top">Alamat</td>
                        <td class="align-top">:</td>
                        <td class="align-top">Ruko KDA Junction Blok C No 8-9 Batam Center</td>
                    </tr>
                </table>
                <p class="mt-1 font-bold pl-6">Selanjutnya disebut sebagai PIHAK PERTAMA</p>
            </div>

            <div class="pl-4 mt-4">
                <table class="w-full">
                    <tr>
                        <td class="w-6 align-top">2.</td>
                        <td class="w-40 align-top">Nama</td>
                        <td class="w-4 align-top">:</td>
                        <td class="align-top font-bold">{{ $bast->pihak_kedua_nama }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="align-top">Jabatan</td>
                        <td class="align-top">:</td>
                        <td class="align-top">{{ $bast->pihak_kedua_jabatan }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="align-top">Perusahaan</td>
                        <td class="align-top">:</td>
                        <td class="align-top">{{ $bast->pihak_kedua_perusahaan }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="align-top">Alamat</td>
                        <td class="align-top">:</td>
                        <td class="align-top">{{ $bast->pihak_kedua_alamat }}</td>
                    </tr>
                </table>
                <p class="mt-1 font-bold pl-6">Selanjutnya disebut sebagai PIHAK KEDUA</p>
            </div>

            <p class="mt-6">Selanjutnya secara bersama-sama PIHAK PERTAMA dan PIHAK KEDUA dalam hal ini disebut <strong>PARA PIHAK</strong>.</p>

            <p>PARA PIHAK sepakat melaksanakan serah terima <strong>{{ $bast->deskripsi_pekerjaan }}</strong> dengan ketentuan sebagai berikut:</p>

            <div class="space-y-4 mt-4 pl-2">
                <div class="avoid-break">
                    <div class="text-center font-bold underline mb-1">Pasal 1</div>
                    <p>PIHAK PERTAMA menyerahkan kepada PIHAK KEDUA dan PIHAK KEDUA menerima penyerahan dari PIHAK PERTAMA yaitu hasil pekerjaan yang telah disepakati dengan baik.</p>
                </div>
                <div class="avoid-break">
                    <div class="text-center font-bold underline mb-1">Pasal 2</div>
                    <p>Sejak penandatangan Berita Acara Serah Terima ini, maka seluruh tanggung jawab berpindah dari PIHAK PERTAMA kepada PIHAK KEDUA.</p>
                </div>
                <div class="avoid-break">
                    <div class="text-center font-bold underline mb-1">Pasal 3</div>
                    <p>Dengan adanya Berita Acara Serah Terima Pekerjaan ini maka PIHAK PERTAMA telah melaksanakan kewajiban dengan baik dan berhak menerima pembayaran tahap ke II dari PIHAK KEDUA sesuai dari kesepakatan.</p>
                </div>
            </div>

            <p class="mt-6">Demikian Berita Acara Serah Terima ini dibuat dan ditanda tangani oleh kedua belah pihak untuk dapat digunakan sebagaimana mestinya.</p>
        </div>

        {{-- 4. TANDA TANGAN --}}
        <div class="mt-16 flex justify-between text-center px-8 avoid-break">
            <div class="w-5/12">
                <p class="mb-4">Pihak Pertama</p>

                {{-- AREA TTD --}}
                <div class="h-24 flex justify-center items-center">
                    <img src="{{ asset('images/ttd.png') }}" class="h-24 object-contain opacity-100">
                </div>

                <p class="font-bold underline uppercase mt-2">{{ $bast->pihak_pertama_nama }}</p>
                <p>PT. Tasniem Gerai Inspirasi</p>
            </div>
            <div class="w-5/12">
                <p class="mb-24">Pihak Kedua</p>
                <p class="font-bold underline uppercase">{{ $bast->pihak_kedua_nama }}</p>
                <p>{{ $bast->pihak_kedua_perusahaan }}</p>
            </div>
        </div>

        {{-- 5. LAMPIRAN FOTO (HALAMAN BARU) --}}
        <div class="page-break"></div>

        <div class="mt-8 sans">
            <h2 class="text-xl font-bold text-center underline uppercase mb-8">LAMPIRAN DOKUMENTASI</h2>

            <div class="grid grid-cols-2 gap-8">
                <div class="space-y-6">
                    <h3 class="text-lg font-bold text-center bg-gray-200 py-2 border border-gray-400">BEFORE (SEBELUM)</h3>
                    @if(isset($beforeImages) && count($beforeImages) > 0)
                    @foreach($beforeImages as $img)
                    <div class="border border-gray-400 p-2 avoid-break">
                        <img src="{{ asset('storage/' . $img) }}" class="w-full h-48 object-cover block" alt="Foto Sebelum">
                    </div>
                    @endforeach
                    @else
                    <p class="text-center italic text-gray-500 py-10 border border-dashed border-gray-300">Tidak ada dokumentasi.</p>
                    @endif
                </div>

                <div class="space-y-6">
                    <h3 class="text-lg font-bold text-center bg-gray-200 py-2 border border-gray-400">AFTER (SESUDAH)</h3>
                    @if(isset($afterImages) && count($afterImages) > 0)
                    @foreach($afterImages as $img)
                    <div class="border border-gray-400 p-2 avoid-break">
                        <img src="{{ asset('storage/' . $img) }}" class="w-full h-48 object-cover block" alt="Foto Sesudah">
                    </div>
                    @endforeach
                    @else
                    <p class="text-center italic text-gray-500 py-10 border border-dashed border-gray-300">Tidak ada dokumentasi.</p>
                    @endif
                </div>
            </div>
        </div>

    </div>

    <script>
        window.onload = function() {
            // Beri jeda 500ms - 1 detik agar browser selesai me-render gambar
            setTimeout(function() {
                window.print();
            }, 800);
        };
    </script>

</body>

</html>