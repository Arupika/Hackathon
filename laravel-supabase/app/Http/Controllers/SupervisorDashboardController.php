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
use Illuminate\Support\Facades\DB; // Import DB Facade untuk query database
use Illuminate\Support\Str; // Import Str Facade untuk UUID

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

        // Kolom yang diizinkan untuk sorting pada tabel tugas.
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
            ->where('submission.status', 'completed')
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
                    $subQuery->where('status', 'completed');
                })->orWhereDoesntHave('submissions');
            })
            ->orderBy('tenggat_task', 'asc')
            ->take(5)
            ->get();

        // Mengirimkan semua data yang diperlukan ke view Blade
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

            $newTaskId = (string) Str::uuid();

            TaskPekerja::create([
                'id_task' => $newTaskId,
                'id_pekerja' => $request->id_pekerja,
                'judul_task' => $request->judul_task,
                'deskripsi_task' => $request->deskripsi_task,
                'tenggat_task' => $request->tenggat_task,
            ]);

            Submission::create([
                'id_sub' => (string) Str::uuid(),
                'id_task' => $newTaskId,
                'status' => 'to do', // Status default saat tugas pertama kali diassign
                'img_url' => null,
            ]);

            return redirect()->route('supervisor.dashboard')->with('success', 'Tugas berhasil ditambahkan!');

        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error adding task: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menambahkan tugas. Silakan coba lagi.')->withInput();
        }
    }

    /**
     * Menampilkan halaman log tugas.
     * Fungsi ini untuk tombol "Lihat Log Tugas" yang menampilkan tugas tercatat,
     * difilter untuk status 'pending' secara default, atau spesifik jika parameter diberikan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $pekerja_id Parameter ID pekerja (opsional, dari URL)
     * @param  string|null  $task_id Parameter ID tugas (opsional, dari URL)
     * @return \Illuminate\Contracts\View\View
     */
    public function taskLog(Request $request, $pekerja_id = null, $task_id = null)
    {
        $tasksQuery = TaskPekerja::with(['pekerja', 'submissions']);

        // Jika task_id diberikan, filter hanya tugas itu (prioritas tertinggi)
        if ($task_id) {
            $tasksQuery->where('id_task', $task_id);
        } else {
            // Defaultnya hanya tampilkan tugas yang 'pending'
            $tasksQuery->whereHas('submissions', function ($query) {
                $query->where('status', 'pending');
            });

            // Jika pekerja_id juga diberikan, filter tugas 'pending' untuk pekerja tersebut
            if ($pekerja_id) {
                $tasksQuery->where('id_pekerja', $pekerja_id);
            }
        }

        // Urutkan berdasarkan tenggat waktu terdekat secara default
        $tasksQuery->orderBy('tenggat_task', 'asc');

        $tasks = $tasksQuery->paginate(10);

        // Jika task_id diberikan tetapi tugas tidak ditemukan
        if ($task_id && $tasks->isEmpty()) {
            return redirect()->route('supervisor.task.log')->with('error', 'Tugas tidak ditemukan.');
        }

        return view('supervisor.task_log', compact('tasks', 'pekerja_id', 'task_id'));
    }

    /**
     * Menandai status tugas (submission) sebagai 'completed'.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $task_id ID tugas yang akan ditandai selesai
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markTaskDone(Request $request, $task_id)
    {
        try {
            // Cari submission terbaru untuk tugas ini yang berstatus 'pending'
            // Kita asumsikan hanya ada satu submission 'pending' yang relevan pada satu waktu
            $submission = Submission::where('id_task', $task_id)
                                    ->where('status', 'pending') // Pastikan hanya submission pending yang bisa di-done
                                    ->latest('created_at') // Ambil yang paling baru jika ada beberapa
                                    ->firstOrFail();

            // Ubah status menjadi 'completed'
            $submission->status = 'completed';
            $submission->save();

            return redirect()->back()->with('success', 'Tugas berhasil ditandai selesai!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Jika submission tidak ditemukan (misal, sudah selesai atau tidak pending)
            return redirect()->back()->with('error', 'Submission pending untuk tugas ini tidak ditemukan atau sudah selesai.');
        } catch (\Exception $e) {
            // Log error untuk debugging lebih lanjut
            Log::error('Error marking task done for task ID ' . $task_id . ': ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menandai tugas selesai. Silakan coba lagi.');
        }
    }
}