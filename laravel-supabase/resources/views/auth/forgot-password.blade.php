<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi Supervisor</title>
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
                Lupa Kata Sandi
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Tidak masalah. Cukup beritahu kami alamat email Anda dan kami akan mengirimkan tautan reset kata sandi yang akan memungkinkan Anda memilih yang baru.
            </p>
        </div>

        {{-- Session Status (untuk pesan sukses setelah mengirim link reset) --}}
        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600 bg-green-100 p-3 rounded-md border border-green-400">
                {{ session('status') }}
            </div>
        @endif

        {{-- Pesan error validasi --}}
        @if ($errors->any())
            <div class="mb-4 bg-red-100 p-3 rounded-md border border-red-400 text-red-700">
                <ul class="mt-3 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="mt-8 space-y-6" method="POST" action="{{ route('password.email') }}">
            @csrf

            {{-- Email Address --}}
            <div>
                <label for="email" class="sr-only">Alamat Email</label>
                <input id="email" class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                    type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="Alamat Email">
            </div>

            <div class="flex items-center justify-end mt-4">
                <button type="submit"
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-blue-500 group-hover:text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    Kirim Tautan Reset Kata Sandi
                </button>
            </div>
        </form>
    </div>
</body>
</html>