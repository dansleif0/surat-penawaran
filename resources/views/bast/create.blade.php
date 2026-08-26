@extends('layouts.app')

@section('content')
{{-- LOGIKA TANGGAL INDONESIA OTOMATIS --}}
@php
    $days = [
        'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
    ];
    $months = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];

    $now = \Carbon\Carbon::now();
    $hariIni = $days[$now->format('l')];
    $tanggalIni = $now->format('d');
    $bulanIni = $months[(int)$now->format('m')];
    $tahunIni = $now->format('Y');
@endphp

<div class="container mx-auto my-12 px-4">
    <div class="max-w-5xl mx-auto bg-white p-8 md:p-12 shadow-lg rounded-lg">

        <div class="flex justify-between items-center mb-8 print:hidden">
            <h1 class="text-2xl font-bold text-gray-800">Buat Berita Acara Serah Terima (BAST)</h1>
            <a href="{{ route('histori.index') }}" class="text-gray-600 hover:text-gray-900 font-medium">
                &larr; Kembali ke Histori
            </a>
        </div>

        {{-- Menampilkan Error Validasi jika ada --}}
        @if ($errors->any())
            <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4">
                <p class="font-bold">Terjadi Kesalahan:</p>
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('bast.store', $offer->id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- KOP SURAT -->
            <header class="border-b-2 border-gray-300 pb-4 mb-8">
                <div class="flex justify-between items-center">
                    <div class="w-1/4">
                        <img src="{{ asset('images/logo-tasniem.png') }}" alt="Logo Tasniem" class="w-32">
                    </div>
                    <div class="w-1/2 text-center">
                        <h1 class="text-xl font-bold text-gray-800">PT. TASNIEM GERAI INSPIRASI</h1>
                        <p class="text-sm font-semibold text-gray-600">(The First Inspiration Center of Jotun Indonesia)</p>
                        <p class="text-xs text-gray-500 mt-2">Komp. Ruko KDA Junction Blok C 8 - 9 Batam Centre, Batam, Kepri - Indonesia</p>
                        <p class="text-xs text-gray-500">Telp : +62 778-7485 999, 7080 549 Fax : +62 778-7485 789</p>
                        <p class="text-xs text-gray-500">E-mail : tgi_team040210@yahoo.com Website : www.jotun.com/ap</p>
                    </div>
                    <div class="w-1/4 flex justify-end">
                        <img src="{{ asset('images/logo-jotun.png') }}" alt="Logo Jotun" class="w-40">
                    </div>
                </div>
            </header>

            <!-- JUDUL -->
            <div class="text-center mb-8">
                <h2 class="text-xl font-bold underline uppercase">BERITA ACARA SERAH TERIMA PEKERJAAN</h2>
                <div class="flex justify-center items-center mt-2 gap-2">
                    <span class="text-gray-700 font-medium">No.</span>
                    <input type="text" name="no_surat" value="{{ old('no_surat', $noSurat) }}" class="border-b border-gray-400 focus:outline-none focus:border-blue-500 text-center w-64" required>
                </div>
            </div>

            <!-- ISI DOKUMEN -->
            <div class="text-justify leading-relaxed text-gray-800 space-y-4">

                <!-- TANGGAL OTOMATIS -->
                <p>
                    Pada hari ini
                    <input type="text" name="hari_ini" value="{{ $hariIni }}" class="border-b border-gray-300 w-24 px-1 focus:outline-none text-center font-semibold" required>
                    tanggal
                    <input type="number" name="tanggal_ini" value="{{ $tanggalIni }}" class="border-b border-gray-300 w-12 px-1 text-center focus:outline-none font-semibold" required>
                    bulan
                    <input type="text" name="bulan_ini" value="{{ $bulanIni }}" class="border-b border-gray-300 w-24 px-1 focus:outline-none text-center font-semibold" required>
                    tahun
                    <input type="number" name="tahun_ini" value="{{ $tahunIni }}" class="border-b border-gray-300 w-16 px-1 text-center focus:outline-none font-semibold" required>
                    telah dilakukan serah terima hasil pekerjaan oleh dan diantara:
                </p>

                <!-- PIHAK PERTAMA -->
                <div class="ml-4 mt-4">
                    <div class="grid grid-cols-[30px_150px_10px_1fr] gap-y-2 items-start">
                        <span>1.</span>
                        <span>Nama</span>
                        <span>:</span>
                        <input type="text" name="pihak_pertama_nama" value="{{ old('pihak_pertama_nama', $pihakPertama['nama']) }}" class="w-full border rounded px-2 py-1 bg-gray-50 focus:bg-white focus:border-blue-500 transition" required>

                        <span></span>
                        <span>Jabatan</span>
                        <span>:</span>
                        <input type="text" name="pihak_pertama_jabatan" value="{{ old('pihak_pertama_jabatan', $pihakPertama['jabatan']) }}" class="w-full border rounded px-2 py-1 bg-gray-50 focus:bg-white focus:border-blue-500 transition" required>

                        <span></span>
                        <span>Perusahaan/Vendor</span>
                        <span>:</span>
                        <input type="text" value="PT. Tasniem Gerai Inspirasi" class="w-full border rounded px-2 py-1 bg-gray-100 text-gray-600 cursor-not-allowed" readonly>

                        <span></span>
                        <span>Alamat</span>
                        <span>:</span>
                        <textarea rows="2" class="w-full border rounded px-2 py-1 bg-gray-100 text-gray-600 cursor-not-allowed" readonly>Ruko KDA Junction Blok C No 8-9 Batam Center - Batam</textarea>
                    </div>
                    <p class="mt-2 ml-8 font-bold">Selanjutnya disebut sebagai PIHAK PERTAMA</p>
                </div>

                <!-- PIHAK KEDUA (UPDATED) -->
                <div class="ml-4 mt-6">
                    <div class="grid grid-cols-[30px_150px_10px_1fr] gap-y-2 items-start">
                        <span>2.</span>
                        <span>Nama</span>
                        <span>:</span>
                        {{-- Nama: Tetap Wajib --}}
                        <input type="text" name="pihak_kedua_nama" value="{{ old('pihak_kedua_nama', $offer->nama_klien) }}" class="w-full border rounded px-2 py-1 focus:border-blue-500 transition font-semibold" required>

                        <span></span>
                        <span>Jabatan</span>
                        <span>:</span>
                        {{-- Jabatan: OPTIONAL (Tidak ada required) --}}
                        <input type="text" name="pihak_kedua_jabatan" placeholder="Jabatan Penerima (Opsional)" class="w-full border rounded px-2 py-1 focus:border-blue-500 transition">

                        <span></span>
                        <span>Perusahaan Penerima</span>
                        <span>:</span>
                        {{-- Perusahaan: KOSONG & OPTIONAL --}}
                        <input type="text" name="pihak_kedua_perusahaan" value="{{ old('pihak_kedua_perusahaan') }}" placeholder="Nama Perusahaan (Opsional)" class="w-full border rounded px-2 py-1 focus:border-blue-500 transition">

                        <span></span>
                        <span>Alamat</span>
                        <span>:</span>
                        {{-- Alamat: OTOMATIS DARI DETAIL PENAWARAN --}}
                        <textarea name="pihak_kedua_alamat" rows="2" class="w-full border rounded px-2 py-1 focus:border-blue-500 transition" required>{{ old('pihak_kedua_alamat', $offer->client_details) }}</textarea>
                    </div>
                    <p class="mt-2 ml-8 font-bold">Selanjutnya disebut sebagai PIHAK KEDUA</p>
                </div>

                <p class="mt-6">Selanjutnya secara bersama-sama PIHAK PERTAMA dan PIHAK KEDUA dalam hal ini disebut <strong>PARA PIHAK</strong>.</p>

                <p>PARA PIHAK sepakat melaksanakan serah terima
                    <input type="text" name="deskripsi_pekerjaan" placeholder="Contoh: pekerjaan pengecatan dinding Exterior..." class="border-b border-gray-400 w-1/2 px-1 focus:outline-none" required>
                    yang telah diselesaikan pihak pertama di
                    <span class="font-semibold">{{ $offer->nama_klien }}</span>
                    dengan ketentuan sebagai berikut:
                </p>

                <!-- PASAL-PASAL -->
                <div class="mt-4 space-y-4">
                    <div>
                        <div class="text-center font-bold">Pasal 1</div>
                        <p>PIHAK PERTAMA menyerahkan kepada PIHAK KEDUA dan PIHAK KEDUA menerima penyerahan dari PIHAK PERTAMA yaitu hasil pekerjaan yang telah disepakati.</p>
                    </div>
                    <div>
                        <div class="text-center font-bold">Pasal 2</div>
                        <p>Sejak penandatangan Berita Acara Serah Terima ini, maka seluruh tanggung jawab berpindah dari PIHAK PERTAMA kepada PIHAK KEDUA.</p>
                    </div>
                    <div>
                        <div class="text-center font-bold">Pasal 3</div>
                        <p>Dengan adanya Berita Acara Serah Terima Pekerjaan ini maka PIHAK PERTAMA telah melaksanakan kewajiban dengan baik.</p>
                    </div>
                </div>

                <p class="mt-6">Demikian Berita Acara Serah Terima ini dibuat dan ditanda tangani oleh kedua belah pihak untuk dapat digunakan sebagaimana mestinya.</p>
            </div>

            <!-- TANDA TANGAN -->
            <div class="mt-12 flex justify-between text-center px-8">
                <div>
                    <p class="mb-20">Pihak Pertama</p>
                    <p class="font-bold underline" id="preview-nama-pertama">Samsu Rizal</p>
                    <p>PT. Tasniem Gerai Inspirasi</p>
                </div>
                <div>
                    <p class="mb-20">Pihak Kedua</p>
                    <p class="font-bold underline">( .................................................... )</p>
                    <p id="preview-perusahaan-kedua">{{ $offer->nama_klien }}</p>
                </div>
            </div>

            <!-- DOKUMENTASI -->
            <fieldset class="border-t-2 border-gray-300 pt-8 mt-12 print:hidden">
                <legend class="text-xl font-bold text-gray-800 px-4 uppercase bg-gray-100 rounded">Dokumentasi Pekerjaan</legend>
                <p class="text-sm text-gray-500 mb-4">Tambahkan baris sebanyak yang diperlukan untuk foto Before & After.</p>

                <div id="documentation-rows" class="space-y-6">
                    <div class="doc-row grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-4 rounded border relative">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">FOTO BEFORE</label>
                            <input type="file" name="before_images[]" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" onchange="previewImageDynamic(this)">
                            <img class="mt-2 h-32 object-cover rounded hidden preview-img">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">FOTO AFTER</label>
                            <input type="file" name="after_images[]" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-green-50 file:text-green-700 hover:file:bg-green-100" onchange="previewImageDynamic(this)">
                            <img class="mt-2 h-32 object-cover rounded hidden preview-img">
                        </div>
                        <button type="button" class="absolute top-2 right-2 text-red-500 hover:text-red-700 remove-row-btn" title="Hapus Baris">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="button" id="add-row-btn" class="bg-gray-200 text-gray-800 font-bold py-2 px-4 rounded hover:bg-gray-300 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                        Tambah Baris Foto
                    </button>
                </div>
            </fieldset>

            <div class="mt-12 text-center print:hidden">
                <button type="submit" class="bg-blue-600 text-white font-bold py-3 px-8 rounded-full shadow-lg hover:bg-blue-700 hover:shadow-xl transition transform hover:-translate-y-1">
                    Simpan & Buat Dokumen BAST
                </button>
            </div>

        </form>
    </div>
</div>

<template id="doc-row-template">
    <div class="doc-row grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-4 rounded border relative mt-4">
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">FOTO BEFORE</label>
            <input type="file" name="before_images[]" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" onchange="previewImageDynamic(this)">
            <img class="mt-2 h-32 object-cover rounded hidden preview-img">
        </div>
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">FOTO AFTER</label>
            <input type="file" name="after_images[]" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-green-50 file:text-green-700 hover:file:bg-green-100" onchange="previewImageDynamic(this)">
            <img class="mt-2 h-32 object-cover rounded hidden preview-img">
        </div>
        <button type="button" class="absolute top-2 right-2 text-red-500 hover:text-red-700 remove-row-btn" title="Hapus Baris">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
    </div>
</template>

<script>
    function previewImageDynamic(input) {
        const preview = input.nextElementSibling;
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('documentation-rows');
        const addBtn = document.getElementById('add-row-btn');
        const template = document.getElementById('doc-row-template');

        addBtn.addEventListener('click', function() {
            const clone = template.content.cloneNode(true);
            container.appendChild(clone);
        });

        container.addEventListener('click', function(e) {
            if (e.target.closest('.remove-row-btn')) {
                if (container.querySelectorAll('.doc-row').length > 1) {
                    e.target.closest('.doc-row').remove();
                } else {
                    alert("Minimal harus ada satu baris dokumentasi.");
                }
            }
        });

        // Update Text Preview
        document.querySelector('input[name="pihak_pertama_nama"]').addEventListener('input', function(e) {
            document.getElementById('preview-nama-pertama').innerText = e.target.value;
        });

        // Update Nama Perusahaan Preview
        // Karena input perusahaan opsional, kita gunakan fallback nama klien jika kosong
        const inputPerusahaan = document.querySelector('input[name="pihak_kedua_perusahaan"]');
        const defaultKlien = "{{ $offer->nama_klien }}";

        inputPerusahaan.addEventListener('input', function(e) {
            const val = e.target.value.trim();
            document.getElementById('preview-perusahaan-kedua').innerText = val ? val : defaultKlien;
        });
    });
</script>
@endsection