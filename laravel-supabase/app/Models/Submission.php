<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// Hapus 'use Illuminate\Support\Str;'

class Submission extends Model
{
    use HasFactory;

    protected $table = 'submission';
    protected $primaryKey = 'id_sub';
    // Hapus baris ini
    // public $incrementing = false;
    protected $keyType = 'string';

    // id_sub harus masuk fillable jika Anda ingin mass assign ID manual
    protected $fillable = [
        'id_sub',
        'id_task', // Ini juga harus string
        'status',
        'img_url',
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

    public function task()
    {
        return $this->belongsTo(TaskPekerja::class, 'id_task', 'id_task');
    }
}   