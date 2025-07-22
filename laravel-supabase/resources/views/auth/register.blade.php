<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Supervisor</title>
    {{-- Memuat CSS dan JS utama yang dikompilasi oleh Vite (termasuk Tailwind CSS) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Anda bisa menambahkan CSS kustom tambahan di sini jika benar-benar diperlukan */
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md px-8 py-10 bg-white rounded-lg shadow-xl space-y-8">
        <div>
            {{-- Logo Hackathon Anda --}}
            <img class="mx-auto h-24 w-auto" src="{{ asset('images/logo_hackaton-removebg-preview.png') }}" alt="Logo Aplikasi">
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Daftar Akun Baru
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">Masuk di sini</a>
            </p>
        </div>

        {{-- Pesan error umum atau sukses dari session Laravel --}}
        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600 bg-green-100 p-3 rounded-md">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 bg-red-100 p-3 rounded-md border border-red-400 text-red-700">
                <ul class="mt-3 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="mt-8 space-y-6" action="{{ route('register') }}" method="POST">
            @csrf

            {{-- Name --}}
            <div>
                <label for="name" class="sr-only">Nama Lengkap</label>
                <input id="name" class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                    type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Nama Lengkap">
            </div>

            {{-- Email Address --}}
            <div class="mt-4">
                <label for="email" class="sr-only">Alamat Email</label>
                <input id="email" class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                    type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="Alamat Email">
            </div>

            {{-- Password --}}
            <div class="mt-4 relative">
                <label for="password" class="sr-only">Kata Sandi</label>
                <input id="password" class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm pr-10"
                    type="password" name="password" required autocomplete="new-password" placeholder="Kata Sandi">
                
                <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5 focus:outline-none">
                    <svg id="eyeOpenPassword" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg id="eyeClosedPassword" class="h-5 w-5 text-gray-500 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 .989-3.119 3.524-5.467 6.643-6.425M12 16a4 4 0 004-4c0-.623-.111-1.22-.324-1.776M12 4c4.478 0 8.268 2.943 9.542 7-.989 3.119-3.524 5.467-6.643 6.425M12 10a4 4 0 01-4 4c-.623 0-1.22-.111-1.776-.324M12 4.058l.745-.745M12 19.942l-.745.745M19.942 12l.745-.745M4.058 12l-.745.745M12 12h.01M21 21l-1.5-1.5M3 3l1.5 1.5" />
                    </svg>
                </button>
            </div>

            {{-- Confirm Password --}}
            <div class="mt-4 relative">
                <label for="password_confirmation" class="sr-only">Konfirmasi Kata Sandi</label>
                <input id="password_confirmation" class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm pr-10"
                    type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Konfirmasi Kata Sandi">
                
                <button type="button" id="togglePasswordConfirmation" class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5 focus:outline-none">
                    <svg id="eyeOpenConfirmation" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg id="eyeClosedConfirmation" class="h-5 w-5 text-gray-500 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 .989-3.119 3.524-5.467 6.643-6.425M12 16a4 4 0 004-4c0-.623-.111-1.22-.324-1.776M12 4c4.478 0 8.268 2.943 9.542 7-.989 3.119-3.524 5.467-6.643 6.425M12 10a4 4 0 01-4 4c-.623 0-1.22-.111-1.776-.324M12 4.058l.745-.745M12 19.942l-.745.745M19.942 12l.745-.745M4.058 12l-.745.745M12 12h.01M21 21l-1.5-1.5M3 3l1.5 1.5" />
                    </svg>
                </button>
            </div>

            <div>
                <button type="submit"
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-blue-500 group-hover:text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    Daftar
                </button>
            </div>
        </form>
    </div>

    {{-- Script JavaScript untuk toggle password --}}
    <script>
        // Fungsi generik untuk toggle password
        function setupPasswordToggle(inputId, toggleButtonId, eyeOpenId, eyeClosedId) {
            const toggleButton = document.getElementById(toggleButtonId);
            const passwordInput = document.getElementById(inputId);
            const eyeOpen = document.getElementById(eyeOpenId);
            const eyeClosed = document.getElementById(eyeClosedId);

            if (toggleButton && passwordInput && eyeOpen && eyeClosed) {
                toggleButton.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);

                    if (type === 'text') {
                        eyeOpen.classList.add('hidden');
                        eyeClosed.classList.remove('hidden');
                    } else {
                        eyeOpen.classList.remove('hidden');
                        eyeClosed.classList.add('hidden');
                    }
                });
            }
        }

        // Setup untuk field Password
        setupPasswordToggle('password', 'togglePassword', 'eyeOpenPassword', 'eyeClosedPassword');

        // Setup untuk field Confirm Password
        setupPasswordToggle('password_confirmation', 'togglePasswordConfirmation', 'eyeOpenConfirmation', 'eyeClosedConfirmation');
    </script>
</body>
</html>