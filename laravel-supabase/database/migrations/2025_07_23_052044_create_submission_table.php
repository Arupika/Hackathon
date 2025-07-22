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
        Schema::create('submission', function (Blueprint $table) {
            $table->string('id_sub')->primary(); // Primary key for submission, string type
            $table->string('id_task'); // Foreign key to task_pekerja, string type
            $table->string('status'); // e.g., 'completed', 'pending', 'revisi', 'rejected'
            $table->text('img_url')->nullable(); // URL for submitted image, nullable
            $table->timestamps(); // created_at and updated_at columns

            // Foreign key constraint (requires task_pekerja table to exist first)
            $table->foreign('id_task')->references('id_task')->on('task_pekerja')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission');
    }
};