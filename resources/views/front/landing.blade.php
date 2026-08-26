<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penawaran.app - Solusi Administrasi Bisnis Modern</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-effect { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">

    <nav class="fixed w-full z-50 glass-effect border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
           <div class="flex items-center gap-3">
    {{-- Gambar Logo --}}
    <img src="{{ asset('images/logo-app.png') }}"
         alt="Logo"
         class="h-10 w-auto object-contain">
                <span class="text-xl font-extrabold tracking-tight">PENAWARAN<span class="text-blue-600">.APP</span></span>
            </div>
            <div class="hidden md:flex items-center gap-8 font-medium text-slate-600">
                <a href="#fitur" class="hover:text-blue-600 transition">Fitur</a>
                <a href="{{ route('login') }}" class="text-slate-900">Masuk</a>
            </div>
        </div>
    </nav>

    <section class="pt-40 pb-20 px-6">
        <div class="max-w-7xl mx-auto text-center">
            <span class="bg-blue-50 text-blue-600 px-4 py-2 rounded-full text-sm font-bold tracking-wide uppercase">🚀 Revolusi Administrasi Proyek</span>
            <h1 class="mt-8 text-5xl md:text-7xl font-extrabold tracking-tight text-slate-900 leading-tight">
                Buat Penawaran & SPK <br> <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">Dalam Hitungan Detik.</span>
            </h1>
            <p class="mt-6 text-xl text-slate-600 max-w-2xl mx-auto">
                Kelola harga produk, buat invoice otomatis, dan pantau histori BAST dalam satu platform terintegrasi. Profesional, cepat, dan mudah.
            </p>
            <div class="mt-10 flex flex-col md:flex-row gap-4 justify-center">
                <a href="{{ route('dashboard') }}" class="bg-slate-900 text-white px-8 py-4 rounded-2xl font-bold text-lg hover:bg-slate-800 transition shadow-xl">Login</a>
            </div>

            <div class="mt-20 relative">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-50 via-transparent to-transparent z-10"></div>
                <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&q=80&w=2426" alt="Preview Dashboard" class="rounded-3xl shadow-2xl border border-slate-200 max-w-5xl mx-auto">
            </div>
        </div>
    </section>

    <section id="fitur" class="py-24 bg-white px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold">Segala yang Anda Butuhkan</h2>
                <p class="text-slate-500 mt-4">Alur kerja otomatis dari penawaran hingga serah terima pekerjaan.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:border-blue-200 transition group">
                    <div class="h-14 w-14 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600 mb-6 group-hover:bg-blue-600 group-hover:text-white transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor font-bold">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Penawaran Cepat</h3>
                    <p class="text-slate-600 leading-relaxed">Pilih produk, masukkan kuantitas, dan cetak PDF penawaran profesional dengan logo perusahaan Anda sendiri.</p>
                </div>

                <div class="p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:border-blue-200 transition group">
                    <div class="h-14 w-14 bg-indigo-100 rounded-2xl flex items-center justify-center text-indigo-600 mb-6 group-hover:bg-indigo-600 group-hover:text-white transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Histori Terpusat</h3>
                    <p class="text-slate-600 leading-relaxed">Cari data lama dengan mudah. Semua Invoice, BAST, dan SPK tersimpan rapi berdasarkan tanggal dan klien.</p>
                </div>

                <div class="p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:border-blue-200 transition group">
                    <div class="h-14 w-14 bg-emerald-100 rounded-2xl flex items-center justify-center text-emerald-600 mb-6 group-hover:bg-emerald-600 group-hover:text-white transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Siap Cetak SPK & BAST</h3>
                    <p class="text-slate-600 leading-relaxed">Otomatisasi dokumen serah terima pekerjaan (BAST) dan Surat Perintah Kerja tanpa tulis ulang data.</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-slate-900 text-slate-400 py-12 px-6">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-3 text-white">
    {{-- Gambar Logo --}}
    <img src="{{ asset('images/logo-app.png') }}"
         alt="Logo"
         class="h-8 w-auto object-contain brightness-0 invert">
                <span class="text-lg font-bold">PENAWARAN.APP</span>
            </div>
            <p>&copy; 2026 Penawaran.app - Dibuat untuk Efisiensi Kerja.</p>
            <div class="flex gap-6">
                <a href="#" class="hover:text-white transition">Syarat & Ketentuan</a>
                <a href="#" class="hover:text-white transition">Kontak</a>
            </div>
        </div>
    </footer>

</body>
</html>