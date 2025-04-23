<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    protected $fillable = ['title', 'fields', 'start_time', 'end_time', 'isactive'];

    protected $casts = [
        'fields' => 'array',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function submissions()
    {
        return $this->hasMany(FormSubmission::class);
    }
}