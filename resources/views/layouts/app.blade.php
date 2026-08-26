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

                        <a href="{{ route('skp.index') }}" class="flex items-center px-4 py-2 text-sm text-slate-400 hover:text-white hover:translate-x-1 transition-transform italic">
                            <span class="mr-2 text-blue-500">•</span> Histori SPK
                        </a>
                    </div>
                </div>
            </nav>

            <div class="p-4 m-4 bg-slate-900/50 rounded-2xl border border-slate-700/50">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold shadow-lg shadow-blue-500/20">
                        {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                    </div>
                    <div class="text-sm flex-1 min-w-0">
                        <p class="font-bold text-white truncate">{{ Auth::user()->name ?? 'Guest User' }}</p>
                        <p class="text-[10px] text-blue-400 font-semibold uppercase tracking-tighter">Super Admin</p>
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
                @yield('content')
            </div>
        </main>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>

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
    </script>
</body>

</html>