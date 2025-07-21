<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ListPekerja extends Model
{
    use HasFactory;

    protected $table = 'list_pekerja';
    protected $primaryKey = 'id_pekerja';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nama_pekerja',
        'nomer_hp',
        'email',
        'alamat',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    // Relasi ke task_pekerja (jika satu pekerja bisa punya banyak tugas)
    public function tasks()
    {
        return $this->hasMany(TaskPekerja::class, 'id_pekerja', 'id_pekerja');
    }
}