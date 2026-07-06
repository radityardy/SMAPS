<?php

namespace App\Services;

use App\Enums\QueueStatus;
use App\Models\Doctor;
use App\Models\Queue;
use Illuminate\Support\Facades\DB;

class QueueService
{
    /**
     * Generate next queue number for a doctor on a given date.
     */
    public function generateQueueNumber(Doctor $doctor, string $date): string
    {
        $lastQueue = Queue::where('doctor_id', $doctor->id)
            ->whereDate('queue_date', $date)
            ->orderByDesc('id')
            ->first();

        $nextNumber = 1;

        if ($lastQueue) {
            $parts = explode('-', $lastQueue->queue_number);
            $nextNumber = ((int) end($parts)) + 1;
        }

        return $doctor->queue_prefix.'-'.$nextNumber;
    }

    /**
     * Create a new queue entry.
     *
     * @param  array{doctor_id: int, patient_name: string, patient_phone: ?string, complaint: ?string}  $data
     */
    public function createQueue(array $data): Queue
    {
        $doctor = Doctor::findOrFail($data['doctor_id']);
        $today = now()->toDateString();

        return DB::transaction(function () use ($data, $doctor, $today) {
            $queueNumber = $this->generateQueueNumber($doctor, $today);

            return Queue::create([
                'doctor_id' => $data['doctor_id'],
                'patient_name' => $data['patient_name'],
                'patient_phone' => $data['patient_phone'] ?? null,
                'complaint' => $data['complaint'] ?? null,
                'queue_number' => $queueNumber,
                'status' => QueueStatus::Waiting,
                'queue_date' => $today,
            ]);
        });
    }

    /**
     * Call next waiting patient for a doctor.
     */
    public function callNext(Doctor $doctor): ?Queue
    {
        $today = now()->toDateString();

        return DB::transaction(function () use ($doctor, $today) {
            // Automatically complete any active (Called or Serving) queue for this doctor
            Queue::where('doctor_id', $doctor->id)
                ->whereDate('queue_date', $today)
                ->whereIn('status', [QueueStatus::Called, QueueStatus::Serving])
                ->update([
                    'status' => QueueStatus::Done,
                    'completed_at' => now(),
                ]);

            $nextQueue = Queue::where('doctor_id', $doctor->id)
                ->whereDate('queue_date', $today)
                ->where('status', QueueStatus::Waiting)
                ->orderBy('id')
                ->first();

            if (! $nextQueue) {
                return null;
            }

            $nextQueue->update([
                'status' => QueueStatus::Called,
                'called_at' => now(),
            ]);

            return $nextQueue->fresh();
        });
    }

    /**
     * Start serving a called patient.
     */
    public function serve(Queue $queue): Queue
    {
        $queue->update([
            'status' => QueueStatus::Serving,
            'served_at' => now(),
        ]);

        return $queue->fresh();
    }

    /**
     * Complete serving a patient.
     */
    public function complete(Queue $queue): Queue
    {
        $queue->update([
            'status' => QueueStatus::Done,
            'completed_at' => now(),
        ]);

        return $queue->fresh();
    }

    /**
     * Skip a queue (mark as skipped).
     */
    public function skip(Queue $queue): Queue
    {
        $queue->update([
            'status' => QueueStatus::Skipped,
        ]);

        return $queue->fresh();
    }

    /**
     * Get current queue status summary for a doctor today.
     *
     * @return array{total: int, waiting: int, called: int, serving: int, done: int, skipped: int, current: ?Queue}
     */
    public function getDoctorQueueSummary(Doctor $doctor): array
    {
        $today = now()->toDateString();

        $queues = Queue::where('doctor_id', $doctor->id)
            ->whereDate('queue_date', $today)
            ->get();

        $current = $queues
            ->whereIn('status', [QueueStatus::Called, QueueStatus::Serving])
            ->sortByDesc('id')
            ->first();

        return [
            'total' => $queues->count(),
            'waiting' => $queues->where('status', QueueStatus::Waiting)->count(),
            'called' => $queues->where('status', QueueStatus::Called)->count(),
            'serving' => $queues->where('status', QueueStatus::Serving)->count(),
            'done' => $queues->where('status', QueueStatus::Done)->count(),
            'skipped' => $queues->where('status', QueueStatus::Skipped)->count(),
            'current' => $current,
        ];
    }
}
