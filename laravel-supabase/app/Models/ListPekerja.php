<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// Hapus 'use Illuminate\Support\Str;' karena kita tidak akan generate UUID di sini

class ListPekerja extends Model
{
    use HasFactory;

    protected $table = 'list_pekerja';
    protected $primaryKey = 'id_pekerja';
    public $incrementing = false;
    protected $keyType = 'string'; // Tipe key adalah string (PEK001)

    // id_pekerja harus masuk fillable jika Anda ingin mass assign ID manual
    protected $fillable = [
        'id_pekerja', 
        'nama_pekerja',
        'nomer_hp',
        'email',
        'alamat',
    ];

    // HAPUS ENTIRELY METHOD BOOT() INI agar tidak generate UUID otomatis
    // protected static function boot()
    // {
    //     parent::boot();
    //     static::creating(function ($model) {
    //         if (empty($model->{$model->getKeyName()})) {
    //             $model->{$model->getKeyName()} = (string) Str::uuid();
    //         }
    //     });
    // }

    public function tasks()
    {
        return $this->hasMany(TaskPekerja::class, 'id_pekerja', 'id_pekerja');
    }
}