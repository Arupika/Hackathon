<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaskPekerja; // Import model TaskPekerja
use App\Models\ListPekerja; // Import model ListPekerja
use App\Models\Submission; // Import Model Submission
use Illuminate\Validation\ValidationException;
use Carbon\Carbon; // Untuk mengelola tanggal
use Illuminate\Support\Facades\Log; // Import Log facade
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // <-- SANGAT PENTING: Import DB Facade untuk transaksi dan lock
use Illuminate\Support\Str; // Untuk generate UUID dan string manipulation

class SupervisorDashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard supervisor utama dengan data ringkasan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        // 1. Mengambil data untuk tabel tugas (utama) dengan pagination dan sorting
        $sortColumn = $request->get('sort_by', 'tenggat_task');
        $sortOrder = $request->get('order_by', 'asc');

        $allowedSortColumns = ['judul_task', 'tenggat_task'];
        if (!in_array($sortColumn, $allowedSortColumns)) {
            $sortColumn = 'tenggat_task';
        }
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'asc';
        }

        $tasks = TaskPekerja::with(['pekerja', 'submissions'])
            ->orderBy($sortColumn, $sortOrder)
            ->paginate(10);

        // 2. Mengambil data untuk dropdown pekerja di form penugasan dan tabel daftar pekerja
        $pekerjaList = ListPekerja::all();

        // 3. Data untuk Grafik Visualisasi Tugas (REAL DATA dari database)
        $totalTasks = TaskPekerja::count();

        $completedTasks = DB::table('task_pekerja')
            ->join('submission', 'task_pekerja.id_task', '=', 'submission.id_task')
            ->whereRaw('LOWER(submission.status) = ?', ['done']) // Status 'Done' (case-insensitive)
            ->distinct('task_pekerja.id_task')
            ->count('task_pekerja.id_task');

        $pendingTasks = $totalTasks - $completedTasks;
        if ($pendingTasks < 0) {
            $pendingTasks = 0;
        }

        // 4. Data untuk Bagian "Tugas Berdasarkan Hari" (Tugas Mendekati Deadline/Kadaluarsa)
        $upcomingTasks = TaskPekerja::with(['pekerja', 'submissions'])
            ->where('tenggat_task', '<=', Carbon::now()->addDays(7)->toDateString())
            ->where(function ($query) {
                $query->whereDoesntHave('submissions', function ($subQuery) {
                    $subQuery->whereRaw('LOWER(status) = ?', ['done']);
                })->orWhereDoesntHave('submissions');
            })
            ->orderBy('tenggat_task', 'asc')
            ->take(5)
            ->get();

        return view('supervisor.dashboard', compact(
            'tasks',
            'pekerjaList',
            'totalTasks',
            'completedTasks',
            'pendingTasks',
            'sortColumn',
            'sortOrder',
            'upcomingTasks'
        ));
    }

    /**
     * Menyimpan tugas baru ke database.
     * Fungsi ini menangani POST request dari form "Assign Tugas Baru".
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeTask(Request $request)
    {
        try {
            $request->validate([
                'id_pekerja' => 'required|exists:list_pekerja,id_pekerja',
                'judul_task' => 'required|string|max:255',
                'deskripsi_task' => 'nullable|string',
                'tenggat_task' => 'required|date|after_or_equal:today',
            ], [
                'id_pekerja.required' => 'Pekerja harus dipilih.',
                'id_pekerja.exists' => 'Pekerja tidak ditemukan.',
                'judul_task.required' => 'Judul tugas wajib diisi.',
                'tenggat_task.required' => 'Tenggat waktu wajib diisi.',
                'tenggat_task.date' => 'Tenggat waktu harus berupa tanggal yang valid.',
                'tenggat_task.after_or_equal' => 'Tenggat waktu tidak boleh di masa lalu.',
            ]);

           // ... di dalam metode storeTask ...
$newTaskId = DB::transaction(function () {
    $lastTask = TaskPekerja::orderBy('id_task', 'desc')->lockForUpdate()->first();

    $lastIdNumber = 0;
    if ($lastTask) {
        preg_match('/TASK(\d+)/', $lastTask->id_task, $matches);
        if (isset($matches[1])) {
            $lastIdNumber = (int) $matches[1];
        }
    }
    $newIdNumber = $lastIdNumber + 1;
    $generatedId = 'TASK' . str_pad($newIdNumber, 4, '0', STR_PAD_LEFT);

    // --- TAMBAHKAN BARIS DEBUGGING INI ---
    //dd([
        //'lastTaskFound' => $lastTask ? $lastTask->id_task : 'No task found',
       // 'lastIdNumberExtracted' => $lastIdNumber,
//'newIdNumberCalculated' => $newIdNumber,
        //'newTaskIdGenerated' => $generatedId
    //]);
    // --- AKHIR BARIS DEBUGGING ---


                return $generatedId;
            }, 5); // Coba ulangi transaksi hingga 5 kali jika terjadi deadlock (khusus PostgreSQL)
            // --- AKHIR LOGIKA GENERASI ID KUSTOM ---

            // Membuat record tugas baru di tabel task_pekerja
            TaskPekerja::create([
                'id_task' => $newTaskId, // Menggunakan ID kustom yang baru digenerate
                'id_pekerja' => $request->id_pekerja,
                'judul_task' => $request->judul_task,
                'deskripsi_task' => $request->deskripsi_task,
                'tenggat_task' => $request->tenggat_task,
            ]);

            // Membuat entri submission default untuk tugas baru dengan status "to do"
            Submission::create([
                'id_sub' => (string) Str::uuid(), // Untuk ID submission, tetap pakai UUID agar lebih mudah unik
                'id_task' => $newTaskId, // Menghubungkan ke tugas dengan ID kustom
                'status' => 'to do', // Status default saat tugas pertama kali diassign (selalu lowercase)
                'img_url' => null,
            ]);

            return redirect()->route('supervisor.dashboard')->with('success', 'Tugas berhasil ditambahkan!');

        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error adding task with custom ID: ' . $e->getMessage() . ' - Full stack: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menambahkan tugas. Silakan coba lagi.')->withInput();
        }
    }

    /**
     * Menampilkan halaman log tugas.
     */
    public function taskLog(Request $request)
    {
        $tasksQuery = TaskPekerja::with(['pekerja', 'submissions']);

        // Dapatkan parameter filter dari query string
        $pekerjaId = $request->query('pekerja_id');
        $taskId = $request->query('task_id');
        $statusFilter = $request->query('status', 'pending');

        if ($taskId) {
            $tasksQuery->where('id_task', $taskId);
            $currentFilterText = "Detail Tugas: " . Str::limit($taskId, 8, '') . "...";
        } else {
            if ($pekerjaId) {
                $tasksQuery->where('id_pekerja', $pekerjaId);
                $pekerja = ListPekerja::find($pekerjaId);
                $currentFilterText = "untuk Pekerja: " . ($pekerja->nama_pekerja ?? $pekerjaId);
            } else {
                $currentFilterText = "Filter Status: " . Str::title($statusFilter);
            }

            $tasksQuery->whereHas('submissions', function ($query) use ($statusFilter) {
                $query->whereRaw('LOWER(status) = ?', [strtolower($statusFilter)]);
            });
        }

        $tasksQuery->orderBy('tenggat_task', 'asc');
        $tasks = $tasksQuery->paginate(10);

        if ($taskId && $tasks->isEmpty()) {
            return redirect()->route('supervisor.task.log')->with('error', 'Tugas tidak ditemukan.');
        }

        return view('supervisor.task_log', compact('tasks', 'pekerjaId', 'taskId', 'statusFilter', 'currentFilterText'));
    }

    /**
     * Menandai status tugas (submission) sebagai 'Done'.
     */
    public function markTaskDone(Request $request, $task_id)
    {
        try {
            $submission = Submission::where('id_task', $task_id)
                                    ->whereRaw('LOWER(status) = ?', ['pending'])
                                    ->latest('created_at')
                                    ->firstOrFail();

            $submission->status = 'Done';
            $submission->save();

            return redirect()->back()->with('success', 'Tugas berhasil ditandai selesai!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Submission pending untuk tugas ini tidak ditemukan atau sudah selesai.');
        } catch (\Exception $e) {
            Log::error('Error marking task done for task ID ' . $task_id . ': ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menandai tugas selesai. Silakan coba lagi.');
        }
    }

    /**
     * Menampilkan halaman detail pekerja dan tugas-tugasnya.
     */
    public function showPekerjaDetail(Request $request, $id_pekerja)
    {
        $pekerja = ListPekerja::findOrFail($id_pekerja);

        $tasks = TaskPekerja::with(['submissions'])
                            ->where('id_pekerja', $id_pekerja)
                            ->orderBy('tenggat_task', 'asc')
                            ->paginate(10);

        return view('supervisor.pekerja_detail', compact('pekerja', 'tasks'));
    }

    /**
     * Menampilkan halaman daftar semua pekerja.
     */
    public function listAllPekerja(Request $request)
    {
        $pekerjaList = ListPekerja::orderBy('nama_pekerja', 'asc')->paginate(10);
        return view('supervisor.pekerja_list', compact('pekerjaList'));
    }
}