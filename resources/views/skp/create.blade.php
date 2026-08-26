@extends('layouts.app')

@section('content')
<div class="container mx-auto my-12 px-4 max-w-5xl">
    {{-- Breadcrumb / Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 print:hidden">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Buat Surat Perintah Kerja</h1>
            <p class="text-gray-500 mt-1 text-sm">Terbitkan perintah kerja resmi berdasarkan penawaran yang disetujui.</p>
        </div>
        <a href="{{ route('skp.index') }}" class="inline-flex items-center text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Histori
        </a>
    </div>

    <form action="{{ route('skp.store', $offer->id) }}" method="POST" class="space-y-8">
        @csrf

        {{-- Main Document Card --}}
        <div class="bg-white shadow-2xl rounded-3xl overflow-hidden border border-gray-100">

            {{-- Header Dokumen --}}
            <div class="bg-gray-800 p-8 text-center text-white">
                <h2 class="text-2xl font-bold tracking-widest uppercase mb-2">Surat Perintah Kerja</h2>
                <div class="h-1 w-24 bg-indigo-500 mx-auto rounded-full"></div>
            </div>

            <div class="p-8 md:p-12 space-y-10">

                {{-- Bagian Referensi & Tanggal --}}
                <div class="bg-indigo-50 rounded-2xl p-6 border border-indigo-100 grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                    <div class="space-y-1">
                        <label class="text-xs font-black text-indigo-400 uppercase tracking-widest">Referensi Penawaran</label>
                        <p class="text-gray-700 leading-relaxed text-sm">
                            Berdasarkan Ref ID: <span class="font-bold text-indigo-700">{{ $refNoPenawaran }}</span><br>
                            Tertanggal: <span class="font-medium text-gray-900">{{ $offer->created_at->format('d F Y') }}</span>
                        </p>
                    </div>
                    <div class="space-y-3">
                        <label class="block text-xs font-black text-indigo-400 uppercase tracking-widest">Jenis Pekerjaan</label>
                        <select name="judul_pekerjaan" class="w-full border-b-2 border-indigo-200 bg-transparent focus:border-indigo-500 outline-none font-bold py-2 transition-all cursor-pointer text-gray-800" required>
                            <option value="" disabled selected>-- Pilih Jenis Pekerjaan --</option>
                            <option value="Renovasi dan Pengecatan Interior">Renovasi dan Pengecatan Interior</option>
                            <option value="Renovasi dan Pengecatan Exterior">Renovasi dan Pengecatan Exterior</option>
                            <option value="Renovasi dan Pengecatan Interior & Exterior">Renovasi dan Pengecatan Interior & Exterior</option>
                        </select>
                    </div>
                </div>

                {{-- Bagian Pihak Terkait --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Pihak I --}}
                    <div class="group">
                        <h3 class="flex items-center text-sm font-black text-blue-600 uppercase tracking-widest mb-4">
                            <span class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center mr-3">1</span>
                            PIHAK I (Pemberi Kerja)
                        </h3>
                        <div class="space-y-4 bg-gray-50 p-6 rounded-2xl group-hover:bg-blue-50/50 transition-colors border border-transparent group-hover:border-blue-100">
                            <div>
                                <label class="text-[10px] font-bold text-gray-400 uppercase">Nama Lengkap</label>
                                <input type="text" name="pihak_satu_nama" id="pihak_satu_nama" value="{{ old('pihak_satu_nama', $offer->nama_klien) }}" class="w-full bg-transparent border-b border-gray-200 py-1 focus:border-blue-500 outline-none font-semibold" required>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-gray-400 uppercase">Perusahaan</label>
                                <input type="text" name="pihak_satu_perusahaan" value="{{ old('pihak_satu_perusahaan', $offer->nama_klien) }}" class="w-full bg-transparent border-b border-gray-200 py-1 focus:border-blue-500 outline-none" placeholder="Opsional">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-gray-400 uppercase">Alamat</label>
                                <textarea name="pihak_satu_alamat" id="alamat_pihak_satu" rows="2" class="w-full bg-transparent border-b border-gray-200 py-1 focus:border-blue-500 outline-none text-sm" required>{{ old('pihak_satu_alamat', $offer->client_details) }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Pihak II --}}
                    <div class="group">
                        <h3 class="flex items-center text-sm font-black text-emerald-600 uppercase tracking-widest mb-4">
                            <span class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center mr-3">2</span>
                            PIHAK II (Penerima Kerja)
                        </h3>
                        <div class="space-y-4 bg-gray-50 p-6 rounded-2xl group-hover:bg-emerald-50/50 transition-colors border border-transparent group-hover:border-emerald-100">
                            <div>
                                <label class="text-[10px] font-bold text-gray-400 uppercase">Nama PJ</label>
                                <input type="text" name="pihak_dua_nama" value="{{ $pihakDua['nama'] }}" class="w-full bg-transparent border-b border-gray-200 py-1 focus:border-emerald-500 outline-none font-semibold" required>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-gray-400 uppercase">Perusahaan</label>
                                <input type="text" name="pihak_dua_perusahaan" value="{{ $pihakDua['perusahaan'] }}" class="w-full bg-transparent border-b border-gray-200 py-1 focus:border-emerald-500 outline-none">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-gray-400 uppercase">Alamat Kantor</label>
                                <textarea name="pihak_dua_alamat" rows="2" class="w-full bg-transparent border-b border-gray-200 py-1 text-sm text-gray-500 cursor-not-allowed" readonly>{{ $pihakDua['alamat'] }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Detail Pelaksanaan & Waktu --}}
                <div class="space-y-6">
                    <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest border-b pb-2">Detail Pelaksanaan & Waktu</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Nomor Surat SPK</label>
                                <input type="text" name="no_surat" value="{{ $noSurat }}" class="w-full rounded-xl border-gray-200 bg-gray-50 py-3 font-mono text-indigo-600 font-bold px-4" required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Lokasi Proyek</label>
                                <input type="text" name="lokasi_pekerjaan" id="lokasi_pekerjaan" class="w-full rounded-xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 py-3 px-4 shadow-sm" placeholder="Alamat lengkap lokasi kerja" required>
                            </div>
                        </div>
                        <div class="bg-gray-50 p-6 rounded-2xl grid grid-cols-1 gap-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase">Durasi (Hari)</label>
                                    <input type="number" id="durasi_angka" class="w-full rounded-lg border-gray-200 py-2" placeholder="60" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase">Mulai</label>
                                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="w-full rounded-lg border-gray-200 py-2" required>
                                </div>
                            </div>
                            <div class="pt-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase">Estimasi Selesai</label>
                                <div class="flex items-center justify-between bg-white p-3 rounded-xl border border-dashed border-indigo-300 mt-1">
                                    <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="hidden">
                                    <span id="info_selesai" class="text-sm font-bold text-indigo-600 italic">Pilih durasi & tanggal mulai...</span>
                                    <svg class="w-5 h-5 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <input type="hidden" name="durasi_hari" id="durasi_hari_text">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Nilai & Diskon - Enterprise Style --}}
                <div>
                    <p class="text-sm font-bold text-gray-600 uppercase">Nilai Pekerjaan Awal:</p>
                    <p class="text-xl font-bold text-gray-800">Rp {{ number_format($offer->total_keseluruhan, 0, ',', '.') }}</p>
                    {{-- Hidden input untuk menyimpan nilai asli dari server --}}
                    <input type="hidden" id="nilai_asli" value="{{ $offer->total_keseluruhan }}">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-600 uppercase">Diskon (Rp):</label>
                    <div class="relative mt-1">
                        <span class="absolute left-3 top-2 text-gray-500 font-semibold">Rp</span>
                        <input type="number" name="diskon" id="input_diskon" value="0"
                            class="w-full border rounded px-3 py-2 pl-10 focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="0">
                    </div>
                </div>

                <div class="bg-white p-3 rounded shadow-sm border border-indigo-200">
                    <p class="text-sm font-bold text-indigo-600 uppercase">Total Akhir (Netto):</p>
                    <p class="text-2xl font-bold text-indigo-700" id="display_total_akhir">
                        Rp {{ number_format($offer->total_keseluruhan, 0, ',', '.') }}
                    </p>
                    {{-- Hidden input untuk dikirim ke Controller --}}
                    <input type="hidden" name="nilai_pekerjaan" id="input_total_akhir" value="{{ $offer->total_keseluruhan }}">
                </div>
            </div>

            {{-- Sistem Pembayaran - Project Ledger Style --}}
            <div class="space-y-4">
                <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Sistem Pembayaran</h3>
                        <p class="text-xs text-slate-400">Tentukan termin pembayaran berdasarkan progres pekerjaan.</p>
                    </div>
                    <button type="button" id="add-payment-btn" class="inline-flex items-center px-4 py-2 bg-slate-900 hover:bg-slate-800 text-black text-xs font-bold rounded-lg transition-all shadow-lg active:scale-95">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Tambah Payment
                    </button>
                </div>

                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <table class="w-full text-left border-collapse" id="payment-table">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.1em] border-b border-slate-100">Description</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.1em] border-b border-slate-100">Schedule Date</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.1em] border-b border-slate-100 text-right">Amount Value</th>
                                <th class="px-6 py-4 border-b border-slate-100"></th>
                            </tr>
                        </thead>
                        <tbody id="payment-rows" class="divide-y divide-slate-50">
                            <tr class="group transition-colors hover:bg-slate-50/30">
                                <td class="px-6 py-4">
                                    <input type="text" name="termin_keterangan[]" placeholder="e.g. Down Payment (30%)" class="w-full bg-transparent border-none focus:ring-0 text-sm font-semibold text-slate-700 placeholder-slate-300">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center text-slate-500">
                                        <input type="date" name="termin_tanggal[]" class="bg-transparent border-none focus:ring-0 text-sm font-medium">
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end font-bold text-slate-800">
                                        <span class="text-[10px] text-slate-300 mr-2 uppercase">Rp</span>
                                        <input type="number" name="termin_jumlah[]" placeholder="0" class="w-32 bg-transparent border-none focus:ring-0 text-right text-sm font-black">
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button type="button" class="text-slate-200 hover:text-red-500 transition-colors remove-row" disabled>
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="pt-8 flex flex-col items-center">
                <button type="submit" class="w-full md:w-auto bg-indigo-600 text-white font-black py-4 px-16 rounded-2xl shadow-xl shadow-indigo-100 hover:bg-indigo-700 hover:scale-[1.02] active:scale-[0.98] transition-all tracking-widest uppercase">
                    Simpan Dokumen & Buat SPK
                </button>
            </div>

        </div>
</div>
</form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // --- 1. OTOMATISASI LOKASI PEKERJAAN ---
        const alamatSumber = document.getElementById('alamat_pihak_satu');
        const lokasiTujuan = document.getElementById('lokasi_pekerjaan');

        if (alamatSumber && lokasiTujuan) {
            if (!lokasiTujuan.value) lokasiTujuan.value = alamatSumber.value;
            alamatSumber.addEventListener('input', function() {
                lokasiTujuan.value = this.value;
            });
        }

        // --- 2. OTOMATISASI TANGGAL & DURASI ---
        const durasiInput = document.getElementById('durasi_angka');
        const mulaiInput = document.getElementById('tanggal_mulai');
        const selesaiInput = document.getElementById('tanggal_selesai');
        const durasiTextHidden = document.getElementById('durasi_hari_text');
        const infoSelesai = document.getElementById('info_selesai');

        function hitungSelesai() {
            const hari = parseInt(durasiInput.value);
            const mulaiStr = mulaiInput.value;

            if (!isNaN(hari) && mulaiStr) {
                const mulai = new Date(mulaiStr);
                const selesai = new Date(mulai);
                selesai.setDate(mulai.getDate() + hari);

                const yyyy = selesai.getFullYear();
                const mm = String(selesai.getMonth() + 1).padStart(2, '0');
                const dd = String(selesai.getDate()).padStart(2, '0');

                selesaiInput.value = `${yyyy}-${mm}-${dd}`;
                durasiTextHidden.value = `${hari} hari kalender`;

                const options = {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                };
                infoSelesai.innerText = `Selesai pada: ${selesai.toLocaleDateString('id-ID', options)}`;
            }
        }

        if (durasiInput && mulaiInput) {
            durasiInput.addEventListener('input', hitungSelesai);
            mulaiInput.addEventListener('change', hitungSelesai);
        }

        // --- 3. LOGIKA DISKON ---
        const nilaiAsli = parseFloat(document.getElementById('nilai_asli').value);
        const inputDiskon = document.getElementById('input_diskon');
        const displayTotalAkhir = document.getElementById('display_total_akhir');
        const inputTotalAkhir = document.getElementById('input_total_akhir');

        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(angka).replace("IDR", "Rp");
        }

        if (inputDiskon) {
            inputDiskon.addEventListener('input', function() {
                let diskon = parseFloat(this.value) || 0;
                if (diskon > nilaiAsli) {
                    diskon = nilaiAsli;
                    this.value = nilaiAsli;
                }
                const totalAkhir = nilaiAsli - diskon;
                displayTotalAkhir.innerText = formatRupiah(totalAkhir);
                inputTotalAkhir.value = totalAkhir;
            });
        }

        // --- 4. TABEL PEMBAYARAN DINAMIS (DISEMPURNAKAN) ---
        const tableBody = document.getElementById('payment-rows');
        const addBtn = document.getElementById('add-payment-btn');

        if (addBtn && tableBody) {
            addBtn.addEventListener('click', function() {
                const newRow = document.createElement('tr');
                newRow.className = "group transition-colors hover:bg-slate-50/30";
                newRow.innerHTML = `
                    <td class="px-6 py-4">
                        <input type="text" name="termin_keterangan[]" placeholder="e.g. Termin II" class="w-full bg-transparent border-none focus:ring-0 text-sm font-semibold text-slate-700 placeholder-slate-300">
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center text-slate-500">
                            <input type="date" name="termin_tanggal[]" class="bg-transparent border-none focus:ring-0 text-sm font-medium">
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end font-bold text-slate-800">
                            <span class="text-[10px] text-slate-300 mr-2 uppercase">Rp</span>
                            <input type="number" name="termin_jumlah[]" placeholder="0" class="w-32 bg-transparent border-none focus:ring-0 text-right text-sm font-black">
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button type="button" class="text-slate-200 hover:text-red-500 transition-colors remove-row">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </td>
                `;
                tableBody.appendChild(newRow);
                updateRemoveButtons();
            });

            tableBody.addEventListener('click', function(e) {
                // Cari button atau elemen di dalam button (svg/path)
                const btn = e.target.closest('.remove-row');
                if (btn) {
                    if (tableBody.rows.length > 1) {
                        btn.closest('tr').remove();
                        updateRemoveButtons();
                    }
                }
            });
        }

        function updateRemoveButtons() {
            const rows = tableBody.querySelectorAll('tr');
            rows.forEach((row, index) => {
                const btn = row.querySelector('.remove-row');
                if (btn) btn.disabled = (rows.length === 1);
            });
        }
    });
</script>
@endsection