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
</body>
</html>