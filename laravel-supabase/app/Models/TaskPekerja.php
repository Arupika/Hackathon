<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// Hapus 'use Illuminate\Support\Str;'

class TaskPekerja extends Model
{
    use HasFactory;

    protected $table = 'task_pekerja';
    protected $primaryKey = 'id_task';
    // Hapus baris ini
    // public $incrementing = false;
    protected $keyType = 'string'; // Tipe key adalah string (untuk id_task)

    // id_task harus masuk fillable jika Anda ingin mass assign ID manual
    protected $fillable = [
        'id_task',
        'id_pekerja', // Ini juga harus string
        'judul_task',
        'deskripsi_task',
        'tenggat_task',
    ];

    // HAPUS ENTIRELY METHOD BOOT() INI
    // protected static function boot()
    // {
    //     parent::boot();
    //     static::creating(function ($model) {
    //         if (empty($model->{$model->getKeyName()})) {
    //             $model->{$model->getKeyName()} = (string) Str::uuid();
    //         }
    //     });
    // }

    protected $casts = [
        'tenggat_task' => 'date',
    ];

    public function pekerja()
    {
        return $this->belongsTo(ListPekerja::class, 'id_pekerja', 'id_pekerja');
    }
    // Relasi ke Submission (TAMBAHKAN INI)
    public function submissions()
    {
        // Sebuah tugas bisa memiliki banyak submission, kita akan mengambil yang terbaru
        return $this->hasMany(Submission::class, 'id_task', 'id_task')->latest();
    }
}