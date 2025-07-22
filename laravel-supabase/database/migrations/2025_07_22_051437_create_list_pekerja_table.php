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
        Schema::create('list_pekerja', function (Blueprint $table) {
            // Menggunakan string sebagai Primary Key (sesuai PEK001)
            $table->string('id_pekerja')->primary();
            $table->string('nama_pekerja');
            $table->string('nomer_hp')->nullable();
            $table->string('email')->nullable();
            $table->text('alamat')->nullable();
            $table->timestamps(); // Menambahkan created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('list_pekerja');
    }
};