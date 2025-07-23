<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pekerja - Supervisor</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Custom CSS jika diperlukan, tapi usahakan pakai Tailwind */
    </style>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">

    {{-- TOP NAVIGATION BAR (Sama seperti Dashboard/Log Tugas) --}}
    <nav class="bg-gray-800 p-4 text-white shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <a href="{{ route('supervisor.dashboard') }}" class="text-xl font-bold">Supervisor Dashboard</a>
            <div class="space-x-4 flex items-center">
                <a href="{{ route('supervisor.dashboard') }}" class="hover:text-gray-300">Dashboard</a>
                <a href="{{ route('supervisor.task.log') }}" class="hover:text-gray-300">Log Tugas</a>
                <a href="{{ route('supervisor.pekerja.list') }}" class="hover:text-gray-300">Pekerja</a> {{-- Link Pekerja ke Daftar Semua Pekerja --}}

                <form method="POST" action="{{ route('logout') }}" class="inline-block">
                    @csrf
                    <button type="submit" class="hover:text-gray-300 cursor-pointer bg-transparent border-none text-white font-bold p-0">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    {{-- MAIN CONTENT AREA: SIDEBAR KIRI + KONTEN UTAMA KANAN --}}
    <div class="flex min-h-[calc(100vh-64px)] mt-4">

        {{-- SIDEBAR KIRI (Sama seperti Dashboard) --}}
        <aside class="w-64 bg-white shadow-md rounded-lg p-6 ml-6 flex flex-col justify-between">
            <div>
                <div class="mb-4 text-center">
                    <div class="w-24 h-24 mx-auto rounded-full bg-gray-200 flex items-center justify-center mb-2">
                        <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-800">{{ Auth::user()->name ?? 'Supervisor' }}</h2>
                    <p class="text-gray-600 text-sm">{{ Auth::user()->email ?? 'email@example.com' }}</p>
                </div>
                <nav class="space-y-2">
                    {{-- Navigasi Sidebar jika ada --}}
                </nav>
            </div>
        </aside>

        {{-- KONTEN UTAMA KANAN: DAFTAR SEMUA PEKERJA --}}
        <main class="flex-1 p-6">
            <div class="container mx-auto p-6 bg-white rounded-lg shadow-xl">
                <h1 class="text-3xl font-bold text-gray-800 mb-6">Daftar Semua Pekerja</h1>

                {{-- Alert Messages (dari session) --}}
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Sukses!</strong>
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif
                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Error!</strong>
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif
                {{-- Validasi Error dari Laravel (SEKARANG DISINI UNTUK MODAL) --}}
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Terjadi Kesalahan Saat Menambahkan Pekerja!</strong>
                        <ul class="mt-2 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Tombol Tambah Pekerja (KEMBALI KE BUTTON UNTUK BUKA MODAL) --}}
                <div class="mb-6 text-right">
                    <button id="addPekerjaButton" type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md shadow-md transition duration-150 ease-in-out">
                        + Tambah Pekerja Baru
                    </button>
                </div>

                <div class="overflow-x-auto relative shadow-md sm:rounded-lg mb-8">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="py-3 px-6">ID Pekerja</th>
                                <th scope="col" class="py-3 px-6">Nama Pekerja</th>
                                <th scope="col" class="py-3 px-6">Nomor HP</th>
                                <th scope="col" class="py-3 px-6">Email</th>
                                <th scope="col" class="py-3 px-6">Alamat</th>
                                <th scope="col" class="py-3 px-6">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pekerjaList as $pekerja)
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <th scope="row" class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap">
                                        {{ Str::limit($pekerja->id_pekerja, 8, '') }}...
                                    </th>
                                    <td class="py-4 px-6">{{ $pekerja->nama_pekerja }}</td>
                                    <td class="py-4 px-6">{{ $pekerja->nomer_hp }}</td>
                                    <td class="py-4 px-6">{{ $pekerja->email }}</td>
                                    <td class="py-4 px-6">{{ $pekerja->alamat }}</td>
                                    <td class="py-4 px-6">
                                        {{-- Link ke halaman detail pekerja --}}
                                        <a href="{{ route('supervisor.pekerja.detail', ['id_pekerja' => $pekerja->id_pekerja]) }}" class="font-medium text-blue-600 hover:underline">Lihat Detail & Tugas</a>
                                        {{-- Tambahkan tombol Edit/Hapus Pekerja di sini jika perlu --}}
                                    </td>
                                </tr>
                            @empty
                                <tr class="bg-white border-b">
                                    <td colspan="6" class="py-4 px-6 text-center text-gray-500">Tidak ada pekerja yang terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination untuk daftar pekerja --}}
                <div class="mt-8">
                    {{ $pekerjaList->links('vendor.pagination.tailwind') }}
                </div>

            </div>
        </main>
    </div>

    {{-- MODAL UNTUK FORM TAMBAH PEKERJA BARU (DIKEMBALIKAN KE SINI) --}}
    <div id="addPekerjaModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Tambah Pekerja Baru</h3>
                <div class="mt-2 px-7 py-3 text-left">
                    <form action="{{ route('supervisor.pekerja.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="nama_pekerja" class="block text-gray-700 text-sm font-bold mb-2">Nama Pekerja:</label>
                            <input type="text" id="nama_pekerja" name="nama_pekerja" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('nama_pekerja') border-red-500 @enderror" required value="{{ old('nama_pekerja') }}">
                            @error('nama_pekerja')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="nomer_hp" class="block text-gray-700 text-sm font-bold mb-2">Nomor HP:</label>
                            <input type="text" id="nomer_hp" name="nomer_hp" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('nomer_hp') border-red-500 @enderror" required value="{{ old('nomer_hp') }}">
                            @error('nomer_hp')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                            <input type="email" id="email" name="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('email') border-red-500 @enderror" required value="{{ old('email') }}">
                            @error('email')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label for="alamat" class="block text-gray-700 text-sm font-bold mb-2">Alamat:</label>
                            <textarea id="alamat" name="alamat" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('alamat') border-red-500 @enderror" required>{{ old('alamat') }}</textarea>
                            @error('alamat')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex items-center justify-end">
                            <button type="button" id="cancelAddPekerjaButton" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mr-2">
                                Batal
                            </button>
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Simpan Pekerja
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script>
        // JavaScript untuk Modal Tambah Pekerja
        const addPekerjaModal = document.getElementById('addPekerjaModal');
        const addPekerjaButton = document.getElementById('addPekerjaButton');
        const cancelAddPekerjaButton = document.getElementById('cancelAddPekerjaButton');

        addPekerjaButton.addEventListener('click', () => {
            addPekerjaModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Mencegah scroll body
        });

        cancelAddPekerjaButton.addEventListener('click', () => {
            addPekerjaModal.classList.add('hidden');
            document.body.style.overflow = ''; // Mengembalikan scroll body
        });

        // Menutup modal jika klik di luar konten modal
        addPekerjaModal.addEventListener('click', (event) => {
            if (event.target === addPekerjaModal) {
                addPekerjaModal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        });

        // ======================================================================
        // LOGIKA BARU: OTOMATIS BUKA MODAL JIKA ADA ERROR VALIDASI DARI BACKEND
        // ======================================================================
        @if ($errors->any())
            window.addEventListener('DOMContentLoaded', (event) => {
                addPekerjaModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            });
        @endif
    </script>
</body>
</html>