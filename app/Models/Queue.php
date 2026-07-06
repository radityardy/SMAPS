<?php

namespace App\Models;

use App\Enums\QueueStatus;
use Database\Factories\QueueFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Queue extends Model
{
    /** @use HasFactory<QueueFactory> */
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'patient_name',
        'patient_phone',
        'complaint',
        'queue_number',
        'status',
        'queue_date',
        'called_at',
        'served_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => QueueStatus::class,
            'queue_date' => 'date',
            'called_at' => 'datetime',
            'served_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}
