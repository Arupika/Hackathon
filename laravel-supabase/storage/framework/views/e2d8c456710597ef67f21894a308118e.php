<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Tugas Supervisor</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <style>
        /* Custom CSS jika diperlukan, tapi usahakan pakai Tailwind */
    </style>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">

    
    <nav class="bg-gray-800 p-4 text-white shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <a href="<?php echo e(route('supervisor.dashboard')); ?>" class="text-xl font-bold">Supervisor Dashboard</a>
            <div class="space-x-4 flex items-center">
                <a href="<?php echo e(route('supervisor.dashboard')); ?>" class="hover:text-gray-300">Dashboard</a>
                <a href="<?php echo e(route('supervisor.task.log')); ?>" class="hover:text-gray-300">Log Tugas</a>
                
                <a href="<?php echo e(route('supervisor.pekerja.list')); ?>" class="hover:text-gray-300">Pekerja</a>

                <form method="POST" action="<?php echo e(route('logout')); ?>" class="inline-block">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="hover:text-gray-300 cursor-pointer bg-transparent border-none text-white font-bold p-0">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    
    <div class="flex min-h-[calc(100vh-64px)] mt-4">

        
        <aside class="w-64 bg-white shadow-md rounded-lg p-6 ml-6 flex flex-col justify-between">
            <div>
                <div class="mb-4 text-center">
                    <div class="w-24 h-24 mx-auto rounded-full bg-gray-200 flex items-center justify-center mb-2">
                        <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-800"><?php echo e(Auth::user()->name ?? 'Supervisor'); ?></h2>
                    <p class="text-gray-600 text-sm"><?php echo e(Auth::user()->email ?? 'email@example.com'); ?></p>
                </div>
                <nav class="space-y-2">
                    
                </nav>
            </div>
        </aside>

        
        <main class="flex-1 p-6">
            <div class="container mx-auto p-6 bg-white rounded-lg shadow-xl">
                <h1 class="text-3xl font-bold text-gray-800 mb-6">
                    Log Tugas <?php if(isset($currentFilterText) && ($pekerjaId || $taskId || strtolower($statusFilter) !== 'pending')): ?> - <?php echo e($currentFilterText); ?> <?php endif; ?>
                </h1>

                
                <?php if(session('success')): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Sukses!</strong>
                        <span class="block sm:inline"><?php echo e(session('success')); ?></span>
                    </div>
                <?php endif; ?>
                <?php if(session('error')): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Error!</strong>
                        <span class="block sm:inline"><?php echo e(session('error')); ?></span>
                    </div>
                <?php endif; ?>

                
                <div class="flex space-x-2 mb-6">
                    
                    <a href="<?php echo e(route('supervisor.task.log', array_merge(request()->except('page', 'status', 'task_id'), ['status' => 'pending']))); ?>"
                       class="px-4 py-2 rounded-md font-semibold text-sm transition duration-150 ease-in-out
                       <?php echo e(strtolower($statusFilter) === 'pending' && !$taskId ? 'bg-orange-600 text-white' : 'bg-orange-100 text-orange-800 hover:bg-orange-200'); ?>">
                        Pending
                    </a>
                    
                    <a href="<?php echo e(route('supervisor.task.log', array_merge(request()->except('page', 'status', 'task_id'), ['status' => 'done']))); ?>"
                       class="px-4 py-2 rounded-md font-semibold text-sm transition duration-150 ease-in-out
                       <?php echo e(strtolower($statusFilter) === 'done' && !$taskId ? 'bg-green-600 text-white' : 'bg-green-100 text-green-800 hover:bg-green-200'); ?>">
                        Done
                    </a>
                    
                    <a href="<?php echo e(route('supervisor.task.log', array_merge(request()->except('page', 'status', 'task_id'), ['status' => 'doing']))); ?>"
                       class="px-4 py-2 rounded-md font-semibold text-sm transition duration-150 ease-in-out
                       <?php echo e(strtolower($statusFilter) === 'doing' && !$taskId ? 'bg-yellow-600 text-white' : 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200'); ?>">
                        Doing
                    </a>
                    
                    <?php if($pekerjaId || $taskId || strtolower($statusFilter) !== 'pending'): ?>
                        <a href="<?php echo e(route('supervisor.task.log')); ?>" class="ml-auto bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline inline-flex items-center">
                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            Hapus Filter
                        </a>
                    <?php endif; ?>
                </div>


                <div class="overflow-x-auto relative shadow-md sm:rounded-lg mb-8">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="py-3 px-6">ID Tugas</th>
                                <th scope="col" class="py-3 px-6">Judul Tugas</th>
                                <th scope="col" class="py-3 px-6">Ditugaskan ke</th>
                                <th scope="col" class="py-3 px-6">Deskripsi</th>
                                <th scope="col" class="py-3 px-6">Tenggat Waktu</th>
                                <th scope="col" class="py-3 px-6">Status</th>
                                <th scope="col" class="py-3 px-6">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $latestSubmission = $task->submissions->first();
                                    $status = $latestSubmission ? $latestSubmission->status : 'to do';

                                    $isOverdue = false;
                                    // UBAH DI SINI: Konversi $status ke huruf kecil untuk perbandingan
                                    $statusLower = strtolower($status); 
                                    
                                    // Periksa apakah status 'pending' ATAU 'doing' DAN tenggat waktu sudah lewat
                                    if (($statusLower === 'pending' || $statusLower === 'doing') && $task->tenggat_task && $task->tenggat_task->isPast()) {
                                        $isOverdue = true;
                                    }

                                    // Tentukan kelas CSS untuk baris tabel
                                    $rowClass = $isOverdue ? 'bg-red-100 border-b hover:bg-red-50' : 'bg-white border-b hover:bg-gray-50';
                                ?>
                                <tr class="<?php echo e($rowClass); ?>">
                                    <td class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap"><?php echo e(Str::limit($task->id_task, 8, '')); ?>...</td>
                                    <td class="py-4 px-6"><?php echo e($task->judul_task); ?></td>
                                    <td class="py-4 px-6"><?php echo e($task->pekerja->nama_pekerja ?? 'N/A'); ?></td>
                                    <td class="py-4 px-6"><?php echo e(Str::limit($task->deskripsi_task, 50)); ?></td>
                                    <td class="py-4 px-6"><?php echo e($task->tenggat_task->format('d M Y')); ?></td>
                                    <td>
                                        <?php
                                            $statusClass = '';
                                            // Tetap gunakan strtolower di sini untuk menampilkan status yang konsisten
                                            switch (strtolower($status)) {
                                                case 'done': $statusClass = 'bg-green-100 text-green-800'; break;
                                                case 'to do': $statusClass = 'bg-gray-200 text-gray-700'; break;
                                                case 'doing': $statusClass = 'bg-yellow-100 text-yellow-800'; break;
                                                case 'pending': $statusClass = 'bg-orange-100 text-orange-800'; break;
                                                default: $statusClass = 'bg-gray-100 text-gray-800'; break;
                                            }
                                        ?>
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold <?php echo e($statusClass); ?>">
                                            <?php echo e(Str::title($status)); ?>

                                        </span>
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        
                                        <?php if($latestSubmission): ?>
                                            <button onclick="showDetailModal('<?php echo e($task->judul_task); ?>', '<?php echo e($task->deskripsi_task); ?>', '<?php echo e(Str::title($latestSubmission->status)); ?>', '<?php echo e($latestSubmission->img_url); ?>')" class="font-medium text-blue-600 hover:underline mr-2">Detail</button>
                                        <?php else: ?>
                                            <span class="text-gray-500">No Submission</span>
                                        <?php endif; ?>

                                        
                                        <?php if(strtolower($status) === 'pending'): ?>
                                            
                                            <button type="button" onclick="showConfirmDoneModal('<?php echo e($task->id_task); ?>')" class="font-medium text-green-600 hover:underline">Done</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr class="bg-white border-b">
                                    <td colspan="7" class="py-4 px-6 text-center text-gray-500">Tidak ada tugas yang tercatat dengan status yang diminta.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                
                <div class="mt-8">
                    <?php echo e($tasks->appends(request()->except('page', 'pekerja_id', 'task_id', 'status'))->links('vendor.pagination.tailwind')); ?>

                </div>

            </div>
        </main>
    </div>

    
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

    
    <div id="confirmDoneModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-sm shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-2">Konfirmasi Selesai Tugas</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">Apakah Anda yakin ingin menandai tugas ini selesai?</p>
                </div>
                <div class="items-center px-4 py-3 flex justify-end space-x-3">
                    <button id="cancelDoneButton" class="px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Batal
                    </button>
                    <button id="confirmDoneButton" class="px-4 py-2 bg-green-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-300">
                        Ya, Selesai!
                    </button>
                    
                    <form id="doneTaskForm" method="POST" action="" class="hidden">
                        <?php echo csrf_field(); ?>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script>
        // JavaScript untuk Modal Detail (yang sudah ada)
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
            document.body.style.overflow = 'hidden'; // Mencegah scroll body saat modal aktif
        }

        closeModalButton.addEventListener('click', () => {
            detailModal.classList.add('hidden');
            document.body.style.overflow = ''; // Mengembalikan scroll body
        });

        // Menutup modal detail jika klik di luar konten modal
        detailModal.addEventListener('click', (event) => {
            if (event.target === detailModal) {
                detailModal.classList.add('hidden');
                document.body.style.overflow = ''; // Mengembalikan scroll body
            }
        });


        // ======================================================
        // JavaScript BARU untuk MODAL KONFIRMASI "DONE"
        // ======================================================
        const confirmDoneModal = document.getElementById('confirmDoneModal');
        const cancelDoneButton = document.getElementById('cancelDoneButton');
        const confirmDoneButton = document.getElementById('confirmDoneButton');
        const doneTaskForm = document.getElementById('doneTaskForm');

        let currentTaskId = null; // Variabel untuk menyimpan ID tugas yang akan diselesaikan

        function showConfirmDoneModal(taskId) {
            currentTaskId = taskId; // Simpan ID tugas
            // Atur action form tersembunyi dengan ID tugas yang sesuai
            doneTaskForm.action = `/supervisor/tasks/${taskId}/done`; // Pastikan rute Anda sesuai
            confirmDoneModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Mencegah scroll body
        }

        function hideConfirmDoneModal() {
            confirmDoneModal.classList.add('hidden');
            document.body.style.overflow = ''; // Mengembalikan scroll body
            currentTaskId = null; // Reset ID tugas
        }

        // Event listener untuk tombol "Batal" pada modal konfirmasi
        cancelDoneButton.addEventListener('click', hideConfirmDoneModal);

        // Event listener untuk tombol "Ya, Selesai!" pada modal konfirmasi
        confirmDoneButton.addEventListener('click', () => {
            if (currentTaskId) {
                doneTaskForm.submit(); // Submit form tersembunyi
            }
            hideConfirmDoneModal(); // Sembunyikan modal setelah submit (atau langsung jika tidak submit)
        });

        // Menutup modal konfirmasi jika klik di luar konten modal
        confirmDoneModal.addEventListener('click', (event) => {
            if (event.target === confirmDoneModal) {
                hideConfirmDoneModal();
            }
        });

    </script>
</body>
</html><?php /**PATH E:\HALL OF CODE\Hackathon\laravel-supabase\resources\views/supervisor/task_log.blade.php ENDPATH**/ ?>