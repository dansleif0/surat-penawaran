@extends('layouts.app')

@section('content')
{{-- LOGIKA MEMFORMAT TANGGAL DARI DATABASE KE TEKS INDONESIA --}}
@php
use Carbon\Carbon;
// Mengambil tanggal dari database
$date = Carbon::parse($bast->tanggal_bast);

$days = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
$months = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];

$hari = $days[$date->format('l')];
$tanggal = $date->format('d');
$bulan = $months[(int)$date->format('m')];
$tahun = $date->format('Y');
@endphp

<div class="container mx-auto my-12 px-4">

    <!-- Tombol Aksi (Akan hilang saat print) -->
    <div class="max-w-5xl mx-auto mb-4 flex justify-between gap-2 print:hidden">
        <a href="{{ route('bast.index') }}" class="bg-gray-200 text-gray-700 font-bold py-2 px-4 rounded hover:bg-gray-300 transition-colors">
            &larr; Kembali ke Histori
        </a>
        <a href="{{ route('bast.print', $bast->id) }}" target="_blank" class="bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 transition-colors">
            🖨️ Print Dokumen (PDF)
        </a>
    </div>

    <!-- Area Dokumen Cetak -->
    <!-- ID 'dokumen-bast' ini yang akan kita isolasi saat print -->
    <div class="max-w-5xl mx-auto bg-white p-8 md:p-16 shadow-lg rounded-lg" id="dokumen-bast">

        {{-- HEADER KOP SURAT --}}
        <header class="w-full mb-6">
            <div class="w-full">
                {{-- Menggunakan class w-full agar gambar memenuhi lebar kontainer --}}
                <img src="{{ asset('images/kopsurat.jpg') }}" alt="Kop Surat PT Tasniem Gerai Inspirasi" class="w-full h-auto">
            </div>
            {{-- Garis merah di bawah tetap dipertahankan atau dihapus sesuai keinginan --}}
            <div class="w-full border-b-[4px] border-[#d32f2f] mt-1"></div>
        </header>


        <!-- JUDUL -->
        <div class="text-center mb-8">
            <h2 class="text-xl font-bold underline uppercase">BERITA ACARA SERAH TERIMA PEKERJAAN</h2>
            <p class="font-medium mt-1">No. {{ $bast->no_surat }}</p>
        </div>

        <!-- ISI -->
        <div class="text-justify leading-relaxed text-gray-900 space-y-4 text-base font-serif">
            <p>
                Pada hari ini <strong>{{ $hari }}</strong> tanggal <strong>{{ $tanggal }}</strong> bulan <strong>{{ $bulan }}</strong> tahun <strong>{{ $tahun }}</strong> telah dilakukan serah terima hasil pekerjaan oleh dan diantara:
            </p>

            <!-- PIHAK 1 -->
            <div class="pl-4 mt-4">
                <table class="w-full">
                    <tr>
                        <td class="w-6 align-top">1.</td>
                        <td class="w-40 align-top">Nama</td>
                        <td class="w-4 align-top">:</td>
                        <td class="align-top font-semibold">{{ $bast->pihak_pertama_nama }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="align-top">Jabatan</td>
                        <td class="align-top">:</td>
                        <td class="align-top">{{ $bast->pihak_pertama_jabatan }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="align-top">Perusahaan/Vendor</td>
                        <td class="align-top">:</td>
                        <td class="align-top">PT. Tasniem Gerai Inspirasi</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="align-top">Alamat</td>
                        <td class="align-top">:</td>
                        <td class="align-top">Ruko KDA Junction Blok C No 8-9 Batam Center - Batam</td>
                    </tr>
                </table>
                <p class="mt-2 font-bold pl-6">Selanjutnya disebut sebagai PIHAK PERTAMA</p>
            </div>

            <!-- PIHAK 2 -->
            <div class="pl-4 mt-6">
                <table class="w-full">
                    <tr>
                        <td class="w-6 align-top">2.</td>
                        <td class="w-40 align-top">Nama</td>
                        <td class="w-4 align-top">:</td>
                        <td class="align-top font-semibold">{{ $bast->pihak_kedua_nama }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="align-top">Jabatan</td>
                        <td class="align-top">:</td>
                        <td class="align-top">{{ $bast->pihak_kedua_jabatan }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="align-top">Perusahaan Penerima</td>
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
                <p class="mt-2 font-bold pl-6">Selanjutnya disebut sebagai PIHAK KEDUA</p>
            </div>

            <p class="mt-6">Selanjutnya secara bersama-sama PIHAK PERTAMA dan PIHAK KEDUA dalam hal ini disebut <strong>PARA PIHAK</strong>.</p>

            <p>PARA PIHAK sepakat melaksanakan serah terima <strong>{{ $bast->deskripsi_pekerjaan }}</strong> dengan ketentuan sebagai berikut:</p>

            <div class="space-y-4 mt-4">
                <div>
                    <div class="text-center font-bold underline mb-1">Pasal 1</div>
                    <p>PIHAK PERTAMA menyerahkan kepada PIHAK KEDUA dan PIHAK KEDUA menerima penyerahan dari PIHAK PERTAMA yaitu hasil pekerjaan yang telah disepakati dengan baik.</p>
                </div>
                <div>
                    <div class="text-center font-bold underline mb-1">Pasal 2</div>
                    <p>Sejak penandatangan Berita Acara Serah Terima ini, maka seluruh tanggung jawab berpindah dari PIHAK PERTAMA kepada PIHAK KEDUA.</p>
                </div>
                <div>
                    <div class="text-center font-bold underline mb-1">Pasal 3</div>
                    <p>Dengan adanya Berita Acara Serah Terima Pekerjaan ini maka PIHAK PERTAMA telah melaksanakan kewajiban dengan baik dan berhak menerima pembayaran tahap ke II dari PIHAK KEDUA sesuai dari kesepakatan.</p>
                </div>
            </div>

            <p class="mt-6">Demikian Berita Acara Serah Terima ini dibuat dan ditanda tangani oleh kedua belah pihak untuk dapat digunakan sebagaimana mestinya.</p>
        </div>

        <!-- TANDA TANGAN -->
        <div class="mt-16 flex justify-between text-center px-4">
            <div class="w-1/2">
                <p class="mb-24">Pihak Pertama</p>
                <p class="font-bold underline uppercase">{{ $bast->pihak_pertama_nama }}</p>
                <p>PT. Tasniem Gerai Inspirasi</p>
            </div>
            <div class="w-1/2">
                <p class="mb-24">Pihak Kedua</p>
                <p class="font-bold underline uppercase">{{ $bast->pihak_kedua_nama }}</p>
                <p>{{ $bast->pihak_kedua_perusahaan }}</p>
            </div>
        </div>

        <!-- HALAMAN BARU: LAMPIRAN FOTO -->
        <div class="page-break"></div>

        <div class="mt-8">
            <h2 class="text-xl font-bold text-center underline uppercase mb-8">LAMPIRAN DOKUMENTASI</h2>

            <div class="grid grid-cols-2 gap-8">
                <!-- Kolom Before -->
                <div class="space-y-8">
                    <h3 class="text-lg font-bold text-center bg-gray-200 py-2 border border-gray-400">BEFORE (SEBELUM)</h3>
                    @if(count($beforeImages) > 0)
                    @foreach($beforeImages as $img)
                    <div class="border border-gray-300 p-2 bg-gray-50 shadow-sm">
                        <div class="aspect-w-4 aspect-h-3">
                            <img src="{{ asset('storage/' . $img) }}" class="w-full h-64 object-cover" alt="Foto Sebelum">
                        </div>
                    </div>
                    @endforeach
                    @else
                    <p class="text-center text-gray-500 italic">Tidak ada foto dokumentasi sebelum.</p>
                    @endif
                </div>

                <!-- Kolom After -->
                <div class="space-y-8">
                    <h3 class="text-lg font-bold text-center bg-gray-200 py-2 border border-gray-400">AFTER (SESUDAH)</h3>
                    @if(count($afterImages) > 0)
                    @foreach($afterImages as $img)
                    <div class="border border-gray-300 p-2 bg-gray-50 shadow-sm">
                        <div class="aspect-w-4 aspect-h-3">
                            <img src="{{ asset('storage/' . $img) }}" class="w-full h-64 object-cover" alt="Foto Sesudah">
                        </div>
                    </div>
                    @endforeach
                    @else
                    <p class="text-center text-gray-500 italic">Tidak ada foto dokumentasi sesudah.</p>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    @media print {

        /* 1. Sembunyikan SEMUA elemen di body */
        body * {
            visibility: hidden;
        }

        /* 2. Tampilkan HANYA container dokumen BAST dan semua isinya */
        #dokumen-bast,
        #dokumen-bast * {
            visibility: visible;
        }

        /* 3. Atur posisi dokumen agar menimpa elemen lain yang tersembunyi */
        #dokumen-bast {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            margin: 0;
            padding: 0;
            box-shadow: none !important;
            /* Hilangkan bayangan kotak */
            border: none !important;
        }

        /* Reset background body agar putih bersih */
        body {
            background-color: white !important;
            font-family: 'Times New Roman', serif;
        }

        /* Pastikan elemen yang harus disembunyikan benar-benar hilang (opsional, karena visibility sudah handle) */
        .print\:hidden {
            display: none !important;
        }

        /* Agar halaman baru untuk lampiran bekerja */
        .page-break {
            page-break-before: always;
        }

        /* Paksa background warna untuk judul kolom (misal abu-abu) tercetak */
        .bg-gray-200 {
            background-color: #e5e7eb !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        @page {
            margin: 2cm;
            /* Margin standar surat resmi */
            size: A4 portrait;
        }
    }
</style>
@endsection