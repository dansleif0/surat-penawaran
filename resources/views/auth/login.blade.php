<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Penawaran.App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 flex items-center justify-center h-screen">

    <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-lg border border-gray-100">

        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                PEN<span class="text-blue-600">AWARAN</span>.APP
            </h1>
            <p class="text-gray-500 text-sm mt-2">Silakan masuk menggunakan username Anda</p>
        </div>

        @if ($errors->any())
            <div class="mb-5 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded text-sm" role="alert">
                <p class="font-bold mb-1">Gagal Masuk</p>
                <p>{{ $errors->first() }}</p>
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf

            <div class="mb-5">
                <label for="username" class="block text-sm font-semibold text-gray-700 mb-1">Username</label>
                <input type="text" name="username" id="username"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                       placeholder="admin_tasniem"
                       value="{{ old('username') }}"
                       required autofocus>
            </div>

            <div class="mb-6 relative">
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                <div class="relative">
                    <input type="password" name="password" id="password"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 pr-10"
                           placeholder="••••••••"
                           required>

                    <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700 focus:outline-none">
                        <svg id="eye-open" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg id="eye-closed" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-blue-700 active:bg-blue-800 transform hover:-translate-y-0.5 transition duration-200 shadow-md">
                Masuk ke Aplikasi
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-gray-100 text-center text-xs text-gray-400">
            &copy; {{ date('Y') }} PT. Tasniem Gerai Inspirasi<br>
            Sistem Manajemen Terintegrasi
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeOpen = document.getElementById('eye-open');
            const eyeClosed = document.getElementById('eye-closed');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            } else {
                passwordInput.type = 'password';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            }
        }
    </script>

</body>
</html>