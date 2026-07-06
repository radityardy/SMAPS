<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQueueRequest;
use App\Http\Resources\QueueResource;
use App\Models\Doctor;
use App\Models\Queue;
use App\Services\QueueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    public function __construct(
        public QueueService $queueService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = Queue::with('doctor.user');

        if ($request->has('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date')) {
            $query->whereDate('queue_date', $request->date);
        } else {
            $query->whereDate('queue_date', now()->toDateString());
        }

        $queues = $query->orderBy('id')->get();

        return response()->json([
            'data' => QueueResource::collection($queues),
            'queues' => QueueResource::collection($queues),
        ]);
    }

    public function store(StoreQueueRequest $request): JsonResponse
    {
        $queue = $this->queueService->createQueue($request->validated());
        $queue->load('doctor.user');

        return response()->json([
            'message' => 'Queue created',
            'data' => new QueueResource($queue),
            'queue' => new QueueResource($queue),
        ], 201);
    }

    public function show(Queue $queue): JsonResponse
    {
        $queue->load('doctor.user');

        return response()->json([
            'data' => new QueueResource($queue),
            'queue' => new QueueResource($queue),
        ]);
    }

    public function call(Queue $queue): JsonResponse
    {
        $queue->update([
            'status' => \App\Enums\QueueStatus::Called,
            'called_at' => now(),
        ]);
        $queue->load('doctor.user');

        return response()->json([
            'message' => 'Patient called',
            'data' => new QueueResource($queue),
            'queue' => new QueueResource($queue),
        ]);
    }

    public function callNext(Doctor $doctor): JsonResponse
    {
        $queue = $this->queueService->callNext($doctor);

        if (! $queue) {
            return response()->json([
                'message' => 'No waiting queue',
            ], 404);
        }

        $queue->load('doctor.user');

        return response()->json([
            'message' => 'Patient called',
            'data' => new QueueResource($queue),
            'queue' => new QueueResource($queue),
        ]);
    }

    public function serve(Queue $queue): JsonResponse
    {
        $queue = $this->queueService->serve($queue);
        $queue->load('doctor.user');

        return response()->json([
            'message' => 'Now serving patient',
            'data' => new QueueResource($queue),
            'queue' => new QueueResource($queue),
        ]);
    }

    public function complete(Queue $queue): JsonResponse
    {
        $queue = $this->queueService->complete($queue);
        $queue->load('doctor.user');

        return response()->json([
            'message' => 'Queue completed',
            'data' => new QueueResource($queue),
            'queue' => new QueueResource($queue),
        ]);
    }

    public function skip(Queue $queue): JsonResponse
    {
        $queue = $this->queueService->skip($queue);
        $queue->load('doctor.user');

        return response()->json([
            'message' => 'Queue skipped',
            'data' => new QueueResource($queue),
            'queue' => new QueueResource($queue),
        ]);
    }

    /**
     * Public endpoint - check queue by queue number.
     */
    public function check(Request $request): JsonResponse
    {
        $queueCode = $request->input('queue_number') ?? $request->input('queue_code');

        if (! $queueCode) {
            return response()->json([
                'message' => 'The queue number or queue code field is required.',
            ], 422);
        }

        $queue = Queue::with('doctor.user')
            ->where('queue_number', $queueCode)
            ->whereDate('queue_date', now()->toDateString())
            ->first();

        if (! $queue) {
            return response()->json([
                'message' => 'Queue not found',
            ], 404);
        }

        return response()->json([
            'data' => new QueueResource($queue),
            'queue' => new QueueResource($queue),
        ]);
    }

    /**
     * Public endpoint - display board for all doctors today.
     */
    public function display(): JsonResponse
    {
        $doctors = Doctor::with('user')
            ->where('is_active', true)
            ->get();

        $queueService = $this->queueService;
        $board = $doctors->map(function (Doctor $doctor) use ($queueService) {
            $summary = $queueService->getDoctorQueueSummary($doctor);

            return [
                'doctor_id' => $doctor->id,
                'doctor_name' => $doctor->user->name,
                'specialization' => $doctor->specialization,
                'total' => $summary['total'],
                'waiting' => $summary['waiting'],
                'current_queue' => $summary['current']?->queue_number,
                'current_status' => $summary['current']?->status,
                'current_updated_at' => $summary['current']?->updated_at?->toIso8601String(),
            ];
        });

        return response()->json([
            'display' => $board,
        ]);
    }
}
