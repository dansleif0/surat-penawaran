<header class="bg-white border-b border-gray-200 sticky top-0 z-10 print:hidden">
    <nav class="container mx-auto px-4 flex justify-between items-center py-3">
        <div class="flex items-center gap-8">
            <a href="{{ url('/') }}" class="font-bold text-lg tracking-wider">PENAWARAN.APP</a>
            <ul class="hidden lg:flex items-center gap-6">
                <li><a href="{{ url('/') }}" class="text-xs uppercase font-medium text-gray-500 hover:text-black">Dashboard</a></li>
                @if(Auth::check() && Auth::user()->role === 'admin')
                <li><a href="{{ route('histori.index') }}" class="text-xs uppercase font-medium text-gray-500 hover:text-black">Histori Penawaran</a></li>
                @endif
                <li>
                    <a href="{{ route('invoice.histori') }}" class="text-xs uppercase font-medium text-gray-500 hover:text-black flex items-center gap-1.5">
                        Histori Invoice
                        @php
                            $headerPersiapanCount = \App\Models\Invoice::where('is_persiapan', true)->whereNull('persiapan_confirmed_at')->count();
                        @endphp
                        @if(Auth::check() && Auth::user()->role !== 'admin' && $headerPersiapanCount > 0)
                            <span class="bg-orange-500 text-white text-[10px] font-black px-1.5 py-0.5 rounded-full leading-none animate-pulse">
                                {{ $headerPersiapanCount }}
                            </span>
                        @endif
                    </a>
                </li>
            </ul>
        </div>
        <div class="flex items-center gap-6">
            @auth
                {{-- Menampilkan nama user yang sedang login --}}
                <span class="hidden lg:block font-medium text-sm">{{ Auth::user()->name }}</span>

                {{-- Form Logout --}}
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-xs font-bold text-red-600 hover:text-red-800 transition uppercase">
                        Logout
                    </button>
                </form>
            @else
                {{-- Opsi Login jika belum login --}}
                <a href="{{ route('login') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 transition uppercase">Login</a>
            @endauth
        </div>
    </nav>
</header>