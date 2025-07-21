<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // Tambahkan ini untuk UUID

class TaskPekerja extends Model
{
    use HasFactory;

    // Nama tabel jika berbeda dari konvensi Laravel (plural, snake_case)
    protected $table = 'task_pekerja';

    // Tentukan primary key dan jenisnya
    protected $primaryKey = 'id_task';
    public $incrementing = false; // Karena menggunakan UUID
    protected $keyType = 'string'; // Karena UUID adalah string

    // Kolom yang bisa diisi (fillable) untuk form
    protected $fillable = [
        'id_pekerja', // Akan diisi dari dropdown pekerja
        'judul_task',
        'deskripsi_task',
        'tenggat_task',
    ];

    // Casting untuk tanggal
    protected $casts = [
        'tenggat_task' => 'date',
    ];

    // Boot method untuk generate UUID otomatis saat membuat data baru
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    // Relasi dengan list_pekerja (jika ada)
    public function pekerja()
    {
        return $this->belongsTo(ListPekerja::class, 'id_pekerja', 'id_pekerja');
    }
}