<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisor Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
        }
        nav {
            background-color: #333;
            color: #fff;
            padding: 10px 0;
            text-align: center;
            margin-bottom: 20px;
        }
        nav a {
            color: #fff;
            text-decoration: none;
            margin: 0 15px;
            font-weight: bold;
        }
        .content {
            margin-top: 20px;
        }
        .section-title {
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
            color: #555;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input[type="text"],
        .form-group input[type="date"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box; /* Untuk memastikan padding tidak menambah lebar */
        }
        .form-group textarea {
            resize: vertical; /* Memungkinkan textarea diubah ukurannya secara vertikal */
            min-height: 80px;
        }
        .btn {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .error-message {
            color: #dc3545;
            font-size: 0.9em;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #f2f2f2;
            cursor: pointer; /* Menunjukkan bisa di-sort */
        }
        table th a {
            text-decoration: none;
            color: #333;
            display: block;
        }
        .pagination {
            margin-top: 20px;
            text-align: center;
        }
        .pagination a, .pagination span {
            display: inline-block;
            padding: 8px 12px;
            margin: 0 4px;
            border: 1px solid #ddd;
            text-decoration: none;
            color: #007bff;
            border-radius: 4px;
        }
        .pagination span.current {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
        .pagination a:hover:not(.active) {
            background-color: #f2f2f2;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .stat-card h3 {
            margin-top: 0;
            color: #495057;
        }
        .stat-card p {
            font-size: 2em;
            font-weight: bold;
            color: #343a40;
            margin-bottom: 0;
        }
        .chart-container {
            width: 100%;
            max-width: 600px; /* Atur lebar maksimum untuk grafik */
            margin: 20px auto; /* Tengahkan grafik */
        }
        .chart-title {
            text-align: center;
            margin-bottom: 15px;
            color: #555;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <nav>
        <a href="{{ route('supervisor.dashboard') }}">Dashboard</a>
        {{-- Anda bisa menambahkan link lain di sini --}}
        <a href="#">Laporan</a>
        <a href="#">Pekerja</a>
        <a href="#">Logout</a>
    </nav>

    <div class="container">
        <h1>Dashboard Supervisor</h1>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Statistik Penting --}}
        <h2 class="section-title">Statistik Umum</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Tugas</h3>
                <p>{{ $totalTasks }}</p>
            </div>
            <div class="stat-card">
                <h3>Tugas Selesai</h3>
                <p>{{ $completedTasks }}</p>
            </div>
            <div class="stat-card">
                <h3>Tugas Belum Selesai</h3>
                <p>{{ $pendingTasks }}</p>
            </div>
        </div>

        {{-- Form untuk Memberi Tugas --}}
        <h2 class="section-title">Beri Tugas Baru</h2>
        <form action="{{ route('supervisor.tasks.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="id_pekerja">Pilih Pekerja:</label>
                <select id="id_pekerja" name="id_pekerja" required>
                    <option value="">-- Pilih Pekerja --</option>
                    @foreach($pekerjaList as $pekerja)
                        <option value="{{ $pekerja->id_pekerja }}" {{ old('id_pekerja') == $pekerja->id_pekerja ? 'selected' : '' }}>
                            {{ $pekerja->nama_pekerja }}
                        </option>
                    @endforeach
                </select>
                @error('id_pekerja')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="judul_task">Judul Tugas:</label>
                <input type="text" id="judul_task" name="judul_task" value="{{ old('judul_task') }}" required>
                @error('judul_task')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="deskripsi_task">Deskripsi:</label>
                <textarea id="deskripsi_task" name="deskripsi_task">{{ old('deskripsi_task') }}</textarea>
                @error('deskripsi_task')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="tenggat_task">Tenggat Waktu:</label>
                <input type="date" id="tenggat_task" name="tenggat_task" value="{{ old('tenggat_task') }}" required>
                @error('tenggat_task')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn">Tambah Tugas</button>
        </form>

        {{-- Tabel Daftar Tugas --}}
        <h2 class="section-title" style="margin-top: 40px;">Daftar Tugas</h2>
        <table>
            <thead>
                <tr>
                    <th>ID Tugas</th>
                    <th>
                        <a href="{{ route('supervisor.dashboard', ['sort_by' => 'judul_task', 'order_by' => ($sortColumn == 'judul_task' && $sortOrder == 'asc') ? 'desc' : 'asc']) }}">
                            Judul Tugas
                            @if ($sortColumn == 'judul_task')
                                @if ($sortOrder == 'asc') &uarr; @else &darr; @endif
                            @endif
                        </a>
                    </th>
                    <th>Pekerja</th>
                    <th>Deskripsi</th>
                    <th>
                        <a href="{{ route('supervisor.dashboard', ['sort_by' => 'tenggat_task', 'order_by' => ($sortColumn == 'tenggat_task' && $sortOrder == 'asc') ? 'desc' : 'asc']) }}">
                            Tenggat Waktu
                            @if ($sortColumn == 'tenggat_task')
                                @if ($sortOrder == 'asc') &uarr; @else &darr; @endif
                            @endif
                        </a>
                    </th>
                    {{-- Asumsi ada kolom 'status' di tabel task_pekerja atau Anda akan mengambilnya dari submission --}}
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tasks as $task)
                    <tr>
                        <td>{{ $task->id_task }}</td>
                        <td>{{ $task->judul_task }}</td>
                        <td>{{ $task->pekerja ? $task->pekerja->nama_pekerja : 'N/A' }}</td>
                        <td>{{ Str::limit($task->deskripsi_task, 50) }}</td>
                        <td>{{ $task->tenggat_task->format('d M Y') }}</td>
                        {{-- Status: Anda perlu mendapatkan status dari tabel submission atau task_pekerja --}}
                        <td>
                            @php
                                // Ini adalah logika placeholder.
                                // Anda perlu menyesuaikan ini dengan cara Anda melacak status tugas.
                                // Misalnya, jika Anda punya relasi $task->submission dan status di sana:
                                // $status = $task->submission->status ?? 'Belum Ada Submission';
                                $statuses = ['Pending', 'Selesai', 'Revisi', 'Kadaluarsa'];
                                echo $statuses[array_rand($statuses)]; // Contoh status acak
                            @endphp
                        </td>
                        <td>
                            <a href="#" class="btn btn-sm">Lihat Detail</a>
                            {{-- Tambahkan tombol edit/hapus di sini --}}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center;">Belum ada tugas yang ditambahkan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="pagination">
            {{ $tasks->appends(request()->except('page'))->links() }}
        </div>

        {{-- Grafik Visualisasi Data (Placeholder) --}}
        <h2 class="section-title" style="margin-top: 40px;">Visualisasi Data Tugas</h2>
        <div class="chart-container">
            <p class="chart-title">Persentase Tugas Selesai vs Belum Selesai</p>
            <canvas id="taskStatusChart"></canvas>
        </div>

        {{-- Notifikasi (Placeholder) --}}
        <h2 class="section-title" style="margin-top: 40px;">Notifikasi</h2>
        <div class="content">
            <p>Di sini akan tampil notifikasi penting (misalnya, tugas baru, submission baru, tugas mendekati tenggat waktu, dll.).</p>
            <ul>
                <li>[Waktu] - [Jenis Notifikasi] - [Isi Notifikasi Singkat]</li>
                <li>...</li>
            </ul>
        </div>

    </div>

    <script>
        // Chart.js untuk Grafik
        const ctx = document.getElementById('taskStatusChart').getContext('2d');
        const taskStatusChart = new Chart(ctx, {
            type: 'doughnut', // Atau 'pie'
            data: {
                labels: ['Selesai', 'Belum Selesai'],
                datasets: [{
                    label: 'Jumlah Tugas',
                    data: [{{ $completedTasks }}, {{ $pendingTasks }}],
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
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed !== null) {
                                    label += context.parsed + ' (' + ((context.parsed / ({{ $completedTasks }} + {{ $pendingTasks }})) * 100).toFixed(2) + '%)';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>