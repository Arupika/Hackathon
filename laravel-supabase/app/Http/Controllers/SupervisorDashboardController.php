<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaskPekerja; // Import model TaskPekerja
use App\Models\ListPekerja; // Import model ListPekerja
use Illuminate\Validation\ValidationException;
use Carbon\Carbon; // Untuk mengelola tanggal
use Illuminate\Support\Facades\Log; // Import Log facade

class SupervisorDashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard supervisor dengan data dan form.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        // 1. Mengambil data untuk tabel tugas dengan pagination dan sorting
        $sortColumn = $request->get('sort_by', 'tenggat_task'); // Default sort by tenggat_task
        $sortOrder = $request->get('order_by', 'asc'); // Default order asc

        // Validasi kolom sorting yang diizinkan
        $allowedSortColumns = ['judul_task', 'tenggat_task', 'status']; // Sesuaikan dengan kolom yang ingin di-sort
        if (!in_array($sortColumn, $allowedSortColumns)) {
            $sortColumn = 'tenggat_task';
        }
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'asc';
        }

        $tasks = TaskPekerja::with('pekerja') // eager load relasi pekerja
            ->orderBy($sortColumn, $sortOrder)
            ->paginate(10); // 10 item per halaman

        // 2. Mengambil data untuk dropdown pekerja (untuk form penugasan)
        $pekerjaList = ListPekerja::select('id_pekerja', 'nama_pekerja')->get();

        // 3. Data untuk Grafik (Contoh Sederhana)
        // Ini adalah placeholder. Anda perlu mengambil data aktual dari Supabase
        // Berdasarkan submission_status, task completion, dll.
        $totalTasks = TaskPekerja::count();
        // Asumsi ada kolom 'status' di tabel task_pekerja atau 'submission'
        // Anda perlu menyesuaikan ini dengan logika status di Supabase Anda
        // Contoh:
        // $completedTasks = TaskPekerja::where('status', 'completed')->count();
        // $pendingTasks = TaskPekerja::where('status', 'pending')->count();

        // Untuk contoh, kita buat data dummy untuk grafik
        $completedTasks = rand(30, 70); // Data dummy
        $pendingTasks = rand(10, 40);   // Data dummy
        $totalTasks = $completedTasks + $pendingTasks;


        return view('supervisor.dashboard', compact(
            'tasks',
            'pekerjaList',
            'totalTasks',
            'completedTasks',
            'pendingTasks',
            'sortColumn',
            'sortOrder'
        ));
    }

    /**
     * Menyimpan tugas baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeTask(Request $request)
    {
        try {
            $request->validate([
                'id_pekerja' => 'required|uuid|exists:list_pekerja,id_pekerja', // Pastikan ID pekerja ada di tabel list_pekerja
                'judul_task' => 'required|string|max:255',
                'deskripsi_task' => 'nullable|string',
                'tenggat_task' => 'required|date|after_or_equal:today', // Tenggat waktu tidak boleh di masa lalu
            ], [
                'id_pekerja.required' => 'Pekerja harus dipilih.',
                'id_pekerja.uuid' => 'Format ID pekerja tidak valid.',
                'id_pekerja.exists' => 'Pekerja tidak ditemukan.',
                'judul_task.required' => 'Judul tugas wajib diisi.',
                'tenggat_task.required' => 'Tenggat waktu wajib diisi.',
                'tenggat_task.date' => 'Tenggat waktu harus berupa tanggal yang valid.',
                'tenggat_task.after_or_equal' => 'Tenggat waktu tidak boleh di masa lalu.',
            ]);

            TaskPekerja::create([
                'id_pekerja' => $request->id_pekerja,
                'judul_task' => $request->judul_task,
                'deskripsi_task' => $request->deskripsi_task,
                'tenggat_task' => $request->tenggat_task,
            ]);

            return redirect()->route('supervisor.dashboard')->with('success', 'Tugas berhasil ditambahkan!');

        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error adding task: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menambahkan tugas. Silakan coba lagi.')->withInput();
        }
    }
}