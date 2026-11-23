<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Penawaran</title>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.css" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --sidebar-bg: #1e293b;
            /* Slate 800 */
            --sidebar-hover: #334155;
            /* Slate 700 */
            --accent-color: #3b82f6;
            /* Blue 500 */
        }

        /* Scrollbar halus untuk Sidebar */
        .overflow-y-auto::-webkit-scrollbar {
            width: 4px;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb {
            background: #475569;
            border-radius: 10px;
        }

        .submenu {
            max-height: 0;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            opacity: 0;
            background: rgba(0, 0, 0, 0.1);
            border-radius: 0.5rem;
            margin: 0 8px;
        }

        .submenu.open {
            max-height: 500px;
            opacity: 1;
            padding: 4px 0;
            margin-top: 4px;
        }

        /* Tambahkan ini di dalam <style> jika ingin garis pemisah yang sangat halus */
        .nav-divider {
            height: 1px;
            background: linear-gradient(to right, transparent, rgba(71, 85, 105, 0.5), transparent);
            margin: 15px 20px;
        }

        .nav-item-active {
            background-color: var(--accent-color) !important;
            color: white !important;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .sidebar-gradient {
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
        }

        /* Animasi ping lambat untuk badge notifikasi persiapan di sidebar */
        @keyframes ping-slow {
            0%, 100% { transform: scale(1); opacity: 1; }
            50%       { transform: scale(1.2); opacity: 0.85; }
        }
        .animate-ping-slow {
            animation: ping-slow 1.6s ease-in-out infinite;
        }
    </style>
</head>

<body class="bg-gray-50 font-sans leading-normal tracking-normal">

    <div class="flex h-screen overflow-hidden">
        <aside class="w-68 sidebar-gradient text-white hidden md:flex md:flex-col flex-shrink-0 z-30 shadow-2xl">

            <div class="p-6 flex items-center justify-center border-b border-slate-700/50 bg-slate-900/20">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                    {{-- Kontainer Logo Tanpa Background Putih --}}
                    <div class="flex items-center justify-center transform transition-transform duration-300 group-hover:scale-110">
                        <img src="{{ asset('images/logo-app.png') }}"
                            alt="Logo"
                            class="h-11 w-auto object-contain drop-shadow-md">
                    </div>

                    {{-- Teks Brand --}}
                    <div class="flex flex-col">
                        <span class="text-lg font-extrabold tracking-wider text-white leading-none">
                            PENAWARAN<span class="text-blue-400">.APP</span>
                        </span>
                        <span class="text-[10px] text-slate-400 font-medium tracking-[0.2em] uppercase mt-1">
                            Management System
                        </span>
                    </div>
                </a>
            </div>

            <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-1">
                <p class="px-4 text-[11px] font-semibold text-slate-500 uppercase tracking-widest mb-2">Main Menu</p>

                <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-slate-300 hover:bg-slate-700/50 hover:text-white rounded-xl transition-all duration-200 group">
                    <span class="mr-3 p-2 bg-slate-800 rounded-lg group-hover:bg-blue-500 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </span>
                    <span class="font-medium">Dashboard</span>
                </a>

                @if(Auth::check() && Auth::user()->role === 'admin')
                    <div class="nav-divider"></div>

                    <a href="{{ route('harga.index') }}" class="flex items-center px-4 py-3 text-slate-300 hover:bg-slate-700/50 hover:text-white rounded-xl transition-all duration-200 group">
                        <span class="mr-3 p-2 bg-slate-800 rounded-lg group-hover:bg-blue-500 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                        </span>
                        <span class="font-medium">Harga Produk</span>
                    </a>

                    <div x-data="{ open: false }">
                        <button @click="open = !open" onclick="toggleSubmenu('penawaran-submenu', 'penawaran-icon')"
                            class="w-full flex items-center justify-between px-4 py-3 text-slate-300 hover:bg-slate-700/50 hover:text-white rounded-xl transition-all group focus:outline-none">
                            <div class="flex items-center">
                                <span class="mr-3 p-2 bg-slate-800 rounded-lg group-hover:bg-blue-500 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </span>
                                <span class="font-medium">Buat Penawaran</span>
                            </div>
                            <svg id="penawaran-icon" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-500 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="penawaran-submenu" class="submenu space-y-1">
                            <a href="{{ route('penawaran.create_combined') }}" class="flex items-center px-4 py-2.5 text-sm text-slate-400 hover:text-white hover:translate-x-1 transition-transform tracking-wide italic">
                                <span class="mr-2 text-blue-500">•</span> Penawaran Proyek
                            </a>
                            <a href="{{ route('penawaran.create_product') }}" class="flex items-center px-4 py-2.5 text-sm text-slate-400 hover:text-white hover:translate-x-1 transition-transform tracking-wide italic">
                                <span class="mr-2 text-blue-500">•</span> Penawaran Produk
                            </a>
                        </div>
                    </div>

                    <div x-data="{ open: false }" class="mt-4">
                        <button @click="open = !open" onclick="toggleSubmenu('histori-submenu', 'histori-icon')"
                            class="w-full flex items-center justify-between px-4 py-3 text-slate-300 hover:bg-slate-700/50 hover:text-white rounded-xl transition-all group focus:outline-none">
                            <div class="flex items-center">
                                <span class="mr-3 p-2 bg-slate-800 rounded-lg group-hover:bg-blue-500 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </span>
                                <span class="font-medium">Histori Data</span>
                            </div>
                            <svg id="histori-icon" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-500 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div id="histori-submenu" class="submenu space-y-1">
                            {{-- Histori Rekapan (Tambahan Baru) --}}
                            <a href="{{ route('recap.index') }}" class="flex items-center px-4 py-2 text-sm text-slate-400 hover:text-white hover:translate-x-1 transition-transform italic">
                                <span class="mr-2 text-emerald-500">•</span> Histori Rekapan Biaya
                            </a>

                            <a href="{{ route('histori.index') }}" class="flex items-center px-4 py-2 text-sm text-slate-400 hover:text-white hover:translate-x-1 transition-transform italic">
                                <span class="mr-2 text-blue-500">•</span> Histori Penawaran
                            </a>

                            <a href="{{ route('invoice.histori') }}" class="flex items-center px-4 py-2 text-sm text-slate-400 hover:text-white hover:translate-x-1 transition-transform italic">
                                <span class="mr-2 text-blue-500">•</span> Histori Invoice
                            </a>

                            <a href="{{ route('bast.index') }}" class="flex items-center px-4 py-2 text-sm text-slate-400 hover:text-white hover:translate-x-1 transition-transform italic">
                                <span class="mr-2 text-blue-500">•</span> Histori BAST
                            </a>

                            {{-- Sementara disembunyikan: Histori SPK --}}
                            {{-- <a href="{{ route('skp.index') }}" class="flex items-center px-4 py-2 text-sm text-slate-400 hover:text-white hover:translate-x-1 transition-transform italic">
                                <span class="mr-2 text-blue-500">•</span> Histori SPK
                            </a> --}}
                        </div>
                    </div>
                @else
                    {{-- Menu Khusus Driver & Staff Gudang --}}
                    <div class="nav-divider"></div>

                    @php
                        $sidebarPersiapanCount = \App\Models\Invoice::where('is_persiapan', true)
                            ->whereNull('persiapan_confirmed_at')
                            ->count();
                    @endphp

                    <a href="{{ route('invoice.histori') }}" class="flex items-center px-4 py-3 text-slate-300 hover:bg-slate-700/50 hover:text-white rounded-xl transition-all duration-200 group {{ request()->routeIs('invoice.*') ? 'bg-slate-700/50 text-white' : '' }}">
                        <span class="relative mr-3 p-2 bg-slate-800 rounded-lg group-hover:bg-blue-500 transition-colors {{ request()->routeIs('invoice.*') ? 'bg-blue-500' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            {{-- Badge orange jika ada notifikasi persiapan --}}
                            @if($sidebarPersiapanCount > 0)
                                <span class="absolute -top-1.5 -right-1.5 flex items-center justify-center w-5 h-5 bg-orange-500 rounded-full text-white text-[9px] font-black ring-2 ring-slate-900 animate-ping-slow">
                                    {{ $sidebarPersiapanCount > 9 ? '9+' : $sidebarPersiapanCount }}
                                </span>
                            @endif
                        </span>
                        <span class="font-medium flex-1">Histori Invoice</span>
                        @if($sidebarPersiapanCount > 0)
                            <span class="bg-orange-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full animate-pulse">
                                {{ $sidebarPersiapanCount }} baru
                            </span>
                        @endif
                    </a>

                    {{-- Histori Rekapan Proyek untuk Staff Gudang --}}
                    @if(Auth::user()->role === 'staff_gudang')
                        @php
                            $registeredProductNames = \App\Models\Product::pluck('nama_produk')->unique()->filter()->values()->toArray();
                            $sidebarRecapWaitingCount = \App\Models\Recap::where(function($q) {
                                    $q->where('status', 'belum_dikonfirmasi')->orWhereNull('status');
                                })
                                ->whereNull('confirmed_at')
                                ->whereHas('items', function($q) use ($registeredProductNames) {
                                    $q->whereIn('material', $registeredProductNames);
                                })
                                ->count();
                        @endphp
                        <a href="{{ route('recap.index') }}" class="flex items-center px-4 py-3 text-slate-300 hover:bg-slate-700/50 hover:text-white rounded-xl transition-all duration-200 group mt-2 {{ request()->routeIs('recap.*') ? 'bg-slate-700/50 text-white' : '' }}">
                            <span class="relative mr-3 p-2 bg-slate-800 rounded-lg group-hover:bg-emerald-500 transition-colors {{ request()->routeIs('recap.*') ? 'bg-emerald-500 text-white' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                {{-- Badge notifikasi jika ada rekapan produk terdaftar yang belum dikonfirmasi --}}
                                @if($sidebarRecapWaitingCount > 0)
                                    <span class="absolute -top-1.5 -right-1.5 flex items-center justify-center w-5 h-5 bg-amber-500 rounded-full text-white text-[9px] font-black ring-2 ring-slate-900 animate-ping-slow">
                                        {{ $sidebarRecapWaitingCount > 9 ? '9+' : $sidebarRecapWaitingCount }}
                                    </span>
                                @endif
                            </span>
                            <span class="font-medium flex-1">Histori Rekapan Proyek</span>
                            @if($sidebarRecapWaitingCount > 0)
                                <span class="bg-amber-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full animate-pulse">
                                    {{ $sidebarRecapWaitingCount }} menunggu
                                </span>
                            @endif
                        </a>
                    @endif
                @endif
            </nav>

            <div class="p-4 m-4 bg-slate-900/50 rounded-2xl border border-slate-700/50">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold shadow-lg shadow-blue-500/20">
                        {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                    </div>
                    <div class="text-sm flex-1 min-w-0">
                        <p class="font-bold text-white truncate">{{ Auth::user()->name ?? 'Guest User' }}</p>
                        <p class="text-[10px] text-blue-400 font-semibold uppercase tracking-tighter">{{ Auth::user()->role_label ?? 'Super Admin' }}</p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="p-2 text-slate-400 hover:text-red-400 hover:bg-red-400/10 rounded-lg transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <main class="flex-1 overflow-x-auto overflow-y-auto bg-[#f8fafc] p-8 z-10 relative">
            <div class="max-w-7xl mx-auto">
                @if(session('error'))
                    <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 flex items-start justify-between shadow-sm">
                        <div class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div class="text-sm font-semibold leading-relaxed">{!! nl2br(e(session('error'))) !!}</div>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-700 text-lg font-bold leading-none ml-4">&times;</button>
                    </div>
                @endif

                @if(session('success'))
                    <div id="flash-success-toast" class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-between shadow-sm transition-all duration-300">
                        <div class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="text-sm font-semibold leading-relaxed">{!! nl2br(e(session('success'))) !!}</div>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-700 text-lg font-bold leading-none">&times;</button>
                    </div>
                    <script>
                        setTimeout(() => {
                            const toast = document.getElementById('flash-success-toast');
                            if (toast) {
                                toast.style.opacity = '0';
                                setTimeout(() => toast.remove(), 300);
                            }
                        }, 4000);
                    </script>
                @endif

                @yield('content')
            </div>
        </main>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <script>
        // Fungsi Sidebar Dropdown
        function toggleSubmenu(submenuId, iconId) {
            const submenu = document.getElementById(submenuId);
            const icon = document.getElementById(iconId);

            if (submenu.classList.contains('open')) {
                submenu.classList.remove('open');
                icon.classList.remove('rotate-180');
            } else {
                submenu.classList.add('open');
                icon.classList.add('rotate-180');
            }
        }

        // Fungsi Manual untuk Dropdown Tabel (Jaga-jaga jika Alpine/Flowbite gagal)
        // Gunakan di view tabel Anda: <button onclick="toggleDropdown('dropdown-row-1')">
        function toggleDropdown(dropdownId) {
            const dropdown = document.getElementById(dropdownId);

            // Tutup semua dropdown lain dulu
            document.querySelectorAll('.table-dropdown-menu').forEach(el => {
                if (el.id !== dropdownId) el.classList.add('hidden');
            });

            // Toggle dropdown target
            if (dropdown) {
                dropdown.classList.toggle('hidden');
            }
        }

        // Tutup dropdown jika klik di luar area
        document.addEventListener('click', function(event) {
            // Cek apakah yang diklik adalah tombol dropdown atau menu itu sendiri
            const isDropdownButton = event.target.closest('[onclick^="toggleDropdown"]');
            const isDropdownMenu = event.target.closest('.table-dropdown-menu');

            if (!isDropdownButton && !isDropdownMenu) {
                document.querySelectorAll('.table-dropdown-menu').forEach(el => {
                    el.classList.add('hidden');
                });
            }
        });

        // =========================================================================
        // Modal Peringatan: Surat Penawaran Sudah Pernah Diubah Menjadi Invoice
        // =========================================================================
        window.showInvoiceWarningModal = function(options) {
            options = options || {};
            const modal = document.getElementById('invoiceWarningModal');
            const card = document.getElementById('invoiceWarningModalCard');
            const noSuratEl = document.getElementById('invModalNoSurat');
            const klienEl = document.getElementById('invModalKlien');
            const invoicesEl = document.getElementById('invModalInvoices');
            const btnCancel = document.getElementById('invModalBtnCancel');
            const btnContinue = document.getElementById('invModalBtnContinue');

            if (!modal) return;

            if (noSuratEl) noSuratEl.textContent = options.noSurat || '-';
            if (klienEl) klienEl.textContent = options.namaKlien || '-';
            if (invoicesEl) invoicesEl.textContent = options.existingInvoices || '-';

            function closeModal() {
                modal.classList.add('opacity-0');
                if (card) {
                    card.classList.remove('scale-100');
                    card.classList.add('scale-95');
                }
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 200);
            }

            btnCancel.onclick = function() {
                closeModal();
                if (options.cancelUrl) {
                    window.location.href = options.cancelUrl;
                } else if (typeof options.onCancel === 'function') {
                    options.onCancel();
                }
            };

            btnContinue.onclick = function() {
                closeModal();
                if (options.url) {
                    window.location.href = options.url;
                } else if (typeof options.onContinue === 'function') {
                    options.onContinue();
                }
            };

            // Tampilkan modal dengan animasi lembut
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                if (card) {
                    card.classList.remove('scale-95');
                    card.classList.add('scale-100');
                }
            }, 10);
        };

        // Tutup modal jika klik background luar
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('invoiceWarningModal');
            if (modal && !modal.classList.contains('hidden') && e.target === modal) {
                const btnCancel = document.getElementById('invModalBtnCancel');
                if (btnCancel) btnCancel.click();
            }
        });

        // Tutup modal jika tekan Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('invoiceWarningModal');
                if (modal && !modal.classList.contains('hidden')) {
                    const btnCancel = document.getElementById('invModalBtnCancel');
                    if (btnCancel) btnCancel.click();
                }
            }
        });

        // =========================================================================
        // Auto-search otomatis saat mengetik tanpa harus klik tombol Cari / Apply
        // =========================================================================
        document.addEventListener('DOMContentLoaded', function () {
            const searchInputs = document.querySelectorAll('form input[name="search"]');
            if (!searchInputs.length) return;

            const isAutoSearch = sessionStorage.getItem('auto_search_active') === '1';
            if (isAutoSearch) {
                sessionStorage.removeItem('auto_search_active');
            }

            searchInputs.forEach(function (searchInput) {
                const form = searchInput.closest('form');
                if (!form) return;

                // Matikan autocomplete bawaan browser agar tidak menutupi tampilan
                searchInput.setAttribute('autocomplete', 'off');

                // Jika halaman baru selesai reload hasil pencarian, kembalikan fokus & kursor di akhir kata
                if (isAutoSearch) {
                    searchInput.focus();
                    const len = searchInput.value.length;
                    try {
                        searchInput.setSelectionRange(len, len);
                    } catch (e) {}
                }

                let debounceTimer = null;
                let lastSubmittedVal = searchInput.value;

                // Tandai jika form disubmit manual via tombol submit
                form.addEventListener('submit', function () {
                    sessionStorage.setItem('auto_search_active', '1');
                });

                // Deteksi setiap ketikan atau saat teks dihapus
                searchInput.addEventListener('input', function () {
                    const currentVal = this.value;
                    if (currentVal === lastSubmittedVal) return;

                    clearTimeout(debounceTimer);

                    // Delay 300ms jika dikosongkan (agar cepat reset), atau 450ms saat mengetik kata
                    const delay = currentVal.trim() === '' ? 300 : 450;

                    debounceTimer = setTimeout(function () {
                        lastSubmittedVal = searchInput.value;
                        sessionStorage.setItem('auto_search_active', '1');
                        form.submit();
                    }, delay);
                });

                // Tangani penekanan tombol Enter agar langsung submit tanpa delay
                searchInput.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        clearTimeout(debounceTimer);
                        lastSubmittedVal = searchInput.value;
                        sessionStorage.setItem('auto_search_active', '1');
                        form.submit();
                    }
                });
            });
        });
    </script>

    {{-- Markup Modal Notifikasi: Surat Penawaran Sudah Pernah Diubah Menjadi Invoice --}}
    <div id="invoiceWarningModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity duration-200 hidden opacity-0" aria-modal="true" role="dialog">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden border border-gray-100 transform transition-transform duration-200 scale-95" id="invoiceWarningModalCard">
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-base font-bold text-gray-900">Data Telah Diubah Menjadi Invoice</h3>
                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                            Surat penawaran ini sudah pernah dibuatkan invoice sebelumnya.
                        </p>
                    </div>
                </div>

                {{-- Detail Informasi Surat & Invoice --}}
                <div class="mt-4 p-3.5 bg-amber-50/70 border border-amber-200/80 rounded-xl space-y-1.5 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">No. Surat:</span>
                        <span class="font-bold text-gray-800" id="invModalNoSurat">-</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Nama Klien:</span>
                        <span class="font-bold text-gray-800 text-right truncate max-w-[200px]" id="invModalKlien">-</span>
                    </div>
                    <div class="flex justify-between items-start pt-1.5 border-t border-amber-200/60">
                        <span class="text-gray-500 whitespace-nowrap">Invoice Terkait:</span>
                        <span class="font-mono font-bold text-amber-800 text-right break-words pl-2" id="invModalInvoices">-</span>
                    </div>
                </div>

                <p class="text-xs text-gray-600 mt-4 leading-relaxed">
                    Apakah Anda ingin membatalkan, atau tetap melanjutkan pembuatan invoice baru untuk penawaran ini?
                </p>
            </div>

            <!-- Footer Tombol -->
            <div class="bg-gray-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-gray-100">
                <button type="button" id="invModalBtnCancel" class="px-4 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 transition shadow-xs cursor-pointer">
                    Batalkan
                </button>
                <button type="button" id="invModalBtnContinue" class="px-4 py-2 text-xs font-bold text-white-700 bg-blue-600 hover:bg-blue-700 rounded-lg transition shadow-sm flex items-center gap-1.5 cursor-pointer">
                    <span>Tetap Lanjutkan &rarr;</span>
                </button>
            </div>
        </div>
    </div>
</body>

</html>