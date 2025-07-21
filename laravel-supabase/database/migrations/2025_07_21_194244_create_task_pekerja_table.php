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
            $table->id();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::create('task_pekerja', function (Blueprint $table) {
    $table->uuid('id_task')->primary(); // Menggunakan UUID untuk primary key seperti Supabase
    $table->uuid('id_pekerja')->nullable(); // Foreign key ke list_pekerja
    $table->string('judul_task');
    $table->text('deskripsi_task')->nullable();
    $table->date('tenggat_task');
    $table->timestamps(); // Untuk created_at dan updated_at
});
    }
};
