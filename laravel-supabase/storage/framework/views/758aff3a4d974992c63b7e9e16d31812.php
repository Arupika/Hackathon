<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisor Dashboard</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Anda bisa menambahkan CSS kustom tambahan di sini jika benar-benar diperlukan,
           tapi sebisa mungkin gunakan kelas Tailwind CSS. */
    </style>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">

    
    <nav class="bg-gray-800 p-4 text-white shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <a href="<?php echo e(route('supervisor.dashboard')); ?>" class="text-xl font-bold">Supervisor Dashboard</a>
            <div class="space-x-4 flex items-center">
                
                <a href="<?php echo e(route('supervisor.dashboard')); ?>" class="hover:text-gray-300">Dashboard</a>
                <a href="<?php echo e(route('supervisor.task.log')); ?>" class="hover:text-gray-300">Log Tugas</a>
                <a href="#" class="hover:text-gray-300">Pekerja</a>

                
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

                <?php if($errors->any()): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Validasi Gagal:</strong>
                        <ul class="mt-2 list-disc list-inside">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>

                
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-800">Daftar Pekerja</h1>
                    <div class="flex space-x-4">
                        <button id="addTaskButton" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                            Tambah Tugas
                        </button>
                        <a href="<?php echo e(route('supervisor.task.log')); ?>" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline inline-block">
                            Lihat Log Tugas
                        </a>
                    </div>
                </div>

                
                <div id="addTaskForm" class="bg-gray-50 p-6 rounded-lg shadow-sm mb-10 <?php echo e($errors->any() ? '' : 'hidden'); ?>">
                    <h2 class="text-2xl font-semibold text-gray-700 mb-4 border-b pb-2">Assign Tugas Baru</h2>
                    <form action="<?php echo e(route('supervisor.tasks.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="mb-4">
                            <label for="id_pekerja" class="block text-gray-700 text-sm font-bold mb-2">Pilih Pekerja:</label>
                            <select id="id_pekerja" name="id_pekerja" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline <?php $__errorArgs = ['id_pekerja'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <option value="">-- Pilih Pekerja --</option>
                                <?php $__currentLoopData = $pekerjaList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pekerja): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($pekerja->id_pekerja); ?>" <?php echo e(old('id_pekerja') == $pekerja->id_pekerja ? 'selected' : ''); ?>>
                                        <?php echo e($pekerja->nama_pekerja); ?> (ID: <?php echo e(Str::limit($pekerja->id_pekerja, 8, '')); ?>...)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['id_pekerja'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs italic mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="mb-4">
                            <label for="judul_task" class="block text-gray-700 text-sm font-bold mb-2">Judul Tugas:</label>
                            <input type="text" id="judul_task" name="judul_task" value="<?php echo e(old('judul_task')); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline <?php $__errorArgs = ['judul_task'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <?php $__errorArgs = ['judul_task'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs italic mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="mb-4">
                            <label for="deskripsi_task" class="block text-gray-700 text-sm font-bold mb-2">Deskripsi:</label>
                            <textarea id="deskripsi_task" name="deskripsi_task" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline h-24 <?php $__errorArgs = ['deskripsi_task'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('deskripsi_task')); ?></textarea>
                            <?php $__errorArgs = ['deskripsi_task'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs italic mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="mb-6">
                            <label for="tenggat_task" class="block text-gray-700 text-sm font-bold mb-2">Tenggat Waktu:</label>
                            <input type="date" id="tenggat_task" name="tenggat_task" value="<?php echo e(old('tenggat_task')); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline <?php $__errorArgs = ['tenggat_task'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <?php $__errorArgs = ['tenggat_task'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs italic mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="flex items-center justify-end">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                                Assign Tugas
                            </button>
                            <button type="button" id="cancelAddTask" class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>

                
                <div class="bg-white shadow-md rounded-lg overflow-x-auto mb-8">
                    <h2 class="text-2xl font-semibold text-gray-700 mb-4 border-b pb-2 px-6 pt-4">Daftar Pekerja</h2>
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
                            <?php $__empty_1 = true; $__currentLoopData = $pekerjaList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pekerja): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <th scope="row" class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap">
                                        <?php echo e(Str::limit($pekerja->id_pekerja, 8, '')); ?>...
                                    </th>
                                    <td class="py-4 px-6"><?php echo e($pekerja->nama_pekerja); ?></td>
                                    <td class="py-4 px-6"><?php echo e($pekerja->nomer_hp); ?></td>
                                    <td class="py-4 px-6"><?php echo e($pekerja->email); ?></td>
                                    <td class="py-4 px-6"><?php echo e($pekerja->alamat); ?></td>
                                    <td class="py-4 px-6">
                                        
                                        <a href="<?php echo e(route('supervisor.task.log', ['pekerja_id' => $pekerja->id_pekerja])); ?>" class="font-medium text-blue-600 hover:underline">Lihat Tugas</a>
                                        
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr class="bg-white border-b">
                                    <td colspan="6" class="py-4 px-6 text-center text-gray-500">Tidak ada pekerja yang terdaftar.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                
                <div class="mt-8 bg-white shadow-md rounded-lg p-6">
                    <h2 class="text-2xl font-semibold text-gray-700 mb-4 border-b pb-2">Tugas Berdasarkan Hari</h2>
                    <?php $__empty_1 = true; $__currentLoopData = $upcomingTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="border-b pb-3 mb-3 last:border-b-0 last:pb-0">
                            <div class="flex justify-between items-center mb-1">
                                <p class="text-lg font-semibold text-gray-800"><?php echo e($task->judul_task); ?></p>
                                <?php
                                    $latestSubmission = $task->submissions->first();
                                    // Default status to 'to do' if no submission exists.
                                    // Use 'status' from latest submission if available.
                                    $status = $latestSubmission ? $latestSubmission->status : 'to do'; 
                                    
                                    $statusClass = '';
                                    switch ($status) {
                                        case 'completed': $statusClass = 'bg-green-100 text-green-800'; break;
                                        case 'to do': $statusClass = 'bg-gray-200 text-gray-700'; break; // 'To Do'
                                        case 'doing': $statusClass = 'bg-yellow-100 text-yellow-800'; break; // 'Doing'
                                        case 'pending': $statusClass = 'bg-orange-100 text-orange-800'; break; // 'Pending' (submission received, waiting approval)
                                        case 'revisi': $statusClass = 'bg-yellow-100 text-yellow-800'; break; // 'Revisi' (existing)
                                        case 'rejected': $statusClass = 'bg-red-100 text-red-800'; break; // 'Rejected' (existing)
                                        default: $statusClass = 'bg-gray-100 text-gray-800'; break; // Fallback
                                    }
                                ?>
                                <span class="px-2 py-1 rounded-full text-xs font-semibold <?php echo e($statusClass); ?>">
                                    <?php echo e(Str::title($status)); ?>

                                </span>
                            </div>
                            <p class="text-sm text-gray-600">Untuk: <?php echo e($task->pekerja->nama_pekerja ?? 'N/A'); ?></p>
                            <p class="text-sm text-gray-600 flex items-center">
                                <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                Tenggat: <?php echo e($task->tenggat_task->format('d M Y')); ?>

                                <?php if($task->tenggat_task->isPast() && $status !== 'completed'): ?>
                                    <span class="ml-2 px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                        KADALUARSA
                                    </span>
                                <?php endif; ?>
                            </p>
                            
                            <a href="<?php echo e(route('supervisor.task.log', ['task_id' => $task->id_task])); ?>" class="text-blue-600 hover:underline text-sm mt-1 block">Lihat Detail</a>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-gray-600">Tidak ada tugas yang mendekati tenggat waktu atau tugas hari ini.</p>
                    <?php endif; ?>
                </div>

                
                <div class="mt-8 bg-white shadow-md rounded-lg p-6">
                    <h2 class="text-2xl font-semibold text-gray-700 mb-4 border-b pb-2">Visualisasi Data Tugas</h2>
                    <div class="max-w-xl mx-auto bg-gray-50 p-6 rounded-lg shadow-sm">
                        <p class="text-center text-gray-600 font-medium mb-4">Persentase Tugas Selesai vs Belum Selesai</p>
                        <canvas id="taskStatusChart"></canvas>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6 text-center">
                        <div class="p-3 bg-blue-50 rounded-md">
                            <p class="text-lg font-semibold text-blue-800">Total</p>
                            <p class="text-2xl font-bold text-blue-900"><?php echo e($totalTasks); ?></p>
                        </div>
                        <div class="p-3 bg-green-50 rounded-md">
                            <p class="text-lg font-semibold text-green-800">Selesai</p>
                            <p class="text-2xl font-bold text-green-900"><?php echo e($completedTasks); ?></p>
                        </div>
                        <div class="p-3 bg-yellow-50 rounded-md">
                            <p class="text-lg font-semibold text-yellow-800">Belum Selesai</p>
                            <p class="text-2xl font-bold text-yellow-900"><?php echo e($pendingTasks); ?></p>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script>
        // JavaScript untuk menampilkan/menyembunyikan form "Tambah Tugas"
        const addTaskButton = document.getElementById('addTaskButton');
        const addTaskForm = document.getElementById('addTaskForm');
        const cancelAddTask = document.getElementById('cancelAddTask');

        if (addTaskButton && addTaskForm && cancelAddTask) {
            addTaskButton.addEventListener('click', () => {
                addTaskForm.classList.remove('hidden');
                addTaskForm.scrollIntoView({ behavior: 'smooth', block: 'start' }); // Scroll ke form
            });

            cancelAddTask.addEventListener('click', () => {
                addTaskForm.classList.add('hidden');
            });
        }

        // Chart.js untuk Grafik (menggunakan data dari controller)
        const ctx = document.getElementById('taskStatusChart');
        if (ctx) {
            const taskStatusChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Selesai', 'Belum Selesai'],
                    datasets: [{
                        label: 'Jumlah Tugas',
                        data: [<?php echo e($completedTasks); ?>, <?php echo e($pendingTasks); ?>],
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.8)', // Hijau untuk Selesai
                            'rgba(255, 99, 132, 0.8)'  // Merah untuk Belum Selesai
                        ],
                        borderColor: [
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 99, 132, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                // Default warna teks legend, sesuaikan jika perlu dengan tema CSS Anda
                                color: 'rgb(55, 65, 81)' // Contoh warna teks gray-700
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed !== null) {
                                        let total = <?php echo e($completedTasks); ?> + <?php echo e($pendingTasks); ?>;
                                        if (total > 0) {
                                            label += context.parsed + ' (' + ((context.parsed / total) * 100).toFixed(2) + '%)';
                                        } else {
                                            label += context.parsed + ' (0.00%)';
                                        }
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>
</body>
</html><?php /**PATH E:\HALL OF CODE\Hackathon\laravel-supabase\resources\views/supervisor/dashboard.blade.php ENDPATH**/ ?>