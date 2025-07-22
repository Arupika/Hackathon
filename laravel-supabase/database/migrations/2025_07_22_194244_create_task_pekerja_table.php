<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('task_pekerja', function (Blueprint $table) {
            // Menggunakan string sebagai Primary Key (sesuai PEK001)
            $table->string('id_task')->primary();
            
            // Foreign key id_pekerja, juga string
            $table->string('id_pekerja')->nullable(); // Foreign key ke list_pekerja
            
            $table->string('judul_task');
            $table->text('deskripsi_task')->nullable();
            $table->date('tenggat_task');
            
            // Menambahkan kolom created_at dan updated_at
            $table->timestamps(); 

            // Menambahkan foreign key constraint (pastikan migrasi list_pekerja sudah dijalankan dulu)
            $table->foreign('id_pekerja')->references('id_pekerja')->on('list_pekerja')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Fungsi down() harusnya menghapus tabel
        Schema::dropIfExists('task_pekerja');
    }
};