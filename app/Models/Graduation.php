<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Graduation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'modality_id',
        'rank',
        'achieved_at',
    ];

    protected $casts = [
        'achieved_at' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(Student::class, 'user_id');
    }

    public function modality()
    {
        return $this->belongsTo(Modality::class);
    }
}
