<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pekerja - Supervisor</title>
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
                <a href="{{ route('supervisor.pekerja.list') }}" class="hover:text-gray-300">Pekerja</a>

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

        {{-- KONTEN UTAMA KANAN: DETAIL PEKERJA DAN TUGAS --}}
        <main class="flex-1 p-6">
            <div class="container mx-auto p-6 bg-white rounded-lg shadow-xl">
                <h1 class="text-3xl font-bold text-gray-800 mb-6">Detail Pekerja: {{ $pekerja->nama_pekerja }}</h1>

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

                {{-- Informasi Detail Pekerja --}}
                <div class="bg-gray-50 p-6 rounded-lg shadow-sm mb-8">
                    <h2 class="text-2xl font-semibold text-gray-700 mb-4 border-b pb-2">Informasi Pekerja</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700">
                        <div><strong>ID Pekerja:</strong> {{ $pekerja->id_pekerja }}</div>
                        <div><strong>Nama:</strong> {{ $pekerja->nama_pekerja }}</div>
                        <div><strong>Nomor HP:</strong> {{ $pekerja->nomer_hp }}</div>
                        <div><strong>Email:</strong> {{ $pekerja->email }}</div>
                        <div class="col-span-1 md:col-span-2"><strong>Alamat:</strong> {{ $pekerja->alamat }}</div>
                    </div>
                    {{-- Tambahkan tombol edit/hapus pekerja di sini jika diinginkan --}}
                </div>

                {{-- Daftar Tugas yang Dipegang Pekerja Ini --}}
                <div class="bg-white shadow-md rounded-lg overflow-x-auto mb-8">
                    <h2 class="text-2xl font-semibold text-gray-700 mb-4 border-b pb-2 px-6 pt-4">Tugas Pekerja Ini</h2>
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="py-3 px-6">ID Tugas</th>
                                <th scope="col" class="py-3 px-6">Judul Tugas</th>
                                <th scope="col" class="py-3 px-6">Deskripsi</th>
                                <th scope="col" class="py-3 px-6">Tenggat Waktu</th>
                                <th scope="col" class="py-3 px-6">Status</th>
                                <th scope="col" class="py-3 px-6">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tasks as $task)
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap">{{ Str::limit($task->id_task, 8, '') }}...</td>
                                    <td class="py-4 px-6">{{ $task->judul_task }}</td>
                                    <td class="py-4 px-6">{{ Str::limit($task->deskripsi_task, 50) }}</td>
                                    <td class="py-4 px-6">{{ $task->tenggat_task->format('d M Y') }}</td>
                                    <td>
                                        @php
                                            $latestSubmission = $task->submissions->first();
                                            $status = $latestSubmission ? $latestSubmission->status : 'to do';

                                            $statusClass = '';
                                            switch (strtolower($status)) {
                                                case 'done': $statusClass = 'bg-green-100 text-green-800'; break;
                                                case 'to do': $statusClass = 'bg-gray-200 text-gray-700'; break;
                                                case 'doing': $statusClass = 'bg-yellow-100 text-yellow-800'; break;
                                                case 'pending': $statusClass = 'bg-orange-100 text-orange-800'; break;
                                                default: $statusClass = 'bg-gray-100 text-gray-800'; break;
                                            }
                                        @endphp
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                                            {{ Str::title($status) }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        {{-- Tombol Detail (menggunakan modal untuk melihat deskripsi dan gambar) --}}
                                        {{-- Diubah: Sekarang hanya tombol detail yang memicu modal, tanpa link terpisah untuk gambar --}}
                                        @if($latestSubmission)
                                            <button onclick="showDetailModal('{{ $task->judul_task }}', '{{ $task->deskripsi_task }}', '{{ Str::title($latestSubmission->status) }}', '{{ $latestSubmission->img_url }}')" class="font-medium text-blue-600 hover:underline">Lihat Detail</button>
                                        @else
                                            <span class="text-gray-500">No Submission</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr class="bg-white border-b">
                                    <td colspan="6" class="py-4 px-6 text-center text-gray-500">Tidak ada tugas yang ditugaskan kepada pekerja ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination untuk tugas pekerja --}}
                <div class="mt-8">
                    {{ $tasks->links('vendor.pagination.tailwind') }}
                </div>

            </div>
        </main>
    </div>

    {{-- MODAL UNTUK DETAIL TUGAS/SUBMISSION (DIKEMBALIKAN DARI task_log.blade.php) --}}
    <div id="detailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modalTitle">Detail Tugas</h3>
                <div class="mt-2 px-7 py-3 text-left">
                    <p class="text-sm text-gray-500 mb-1"><strong>Judul:</strong> <span id="modalTaskTitle"></span></p>
                    <p class="text-sm text-gray-500 mb-1"><strong>Deskripsi:</strong> <span id="modalTaskDesc"></span></p>
                    <p class="text-sm text-gray-500 mb-1"><strong>Status:</strong> <span id="modalStatus"></span></p>
                    
                    <div id="modalImageContainer" class="mt-4 hidden">
                        <p class="text-sm text-gray-500 mb-2"><strong>Gambar Submission:</strong></p>
                        <img id="modalImage" src="" alt="Submission Image" class="max-w-full h-auto rounded-md border object-contain">
                    </div>
                    <p id="noImageMessage" class="text-sm text-gray-500 mt-2 hidden">Tidak ada gambar submission.</p>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="closeModalButton" class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // JavaScript untuk Modal Detail (sama persis seperti di task_log.blade.php)
        const detailModal = document.getElementById('detailModal');
        const closeModalButton = document.getElementById('closeModalButton');
        const modalTaskTitle = document.getElementById('modalTaskTitle');
        const modalTaskDesc = document.getElementById('modalTaskDesc');
        const modalStatus = document.getElementById('modalStatus');
        const modalImageContainer = document.getElementById('modalImageContainer');
        const modalImage = document.getElementById('modalImage');
        const noImageMessage = document.getElementById('noImageMessage');

        function showDetailModal(title, description, status, imageUrl) {
            modalTaskTitle.textContent = title;
            modalTaskDesc.textContent = description;
            modalStatus.textContent = status;

            if (imageUrl && imageUrl !== 'null' && imageUrl.trim() !== '') {
                modalImage.src = imageUrl;
                modalImageContainer.classList.remove('hidden');
                noImageMessage.classList.add('hidden');
            } else {
                modalImage.src = '';
                modalImageContainer.classList.add('hidden');
                noImageMessage.classList.remove('hidden');
            }

            detailModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        closeModalButton.addEventListener('click', () => {
            detailModal.classList.add('hidden');
            document.body.style.overflow = '';
        });

        detailModal.addEventListener('click', (event) => {
            if (event.target === detailModal) {
                detailModal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        });
    </script>
</body>
</html>