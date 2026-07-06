<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;
use App\Http\Resources\DoctorResource;
use App\Http\Resources\QueueResource;
use App\Models\Doctor;
use App\Models\User;
use App\Services\QueueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DoctorController extends Controller
{
    public function index(): JsonResponse
    {
        $doctors = Doctor::with('user')
            ->where('is_active', true)
            ->get();

        return response()->json([
            'data' => DoctorResource::collection($doctors),
            'doctors' => DoctorResource::collection($doctors),
        ]);
    }

    public function store(StoreDoctorRequest $request): JsonResponse
    {
        $data = $request->validated();

        $doctor = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => UserRole::Doctor,
                'phone' => $data['phone'] ?? null,
            ]);

            return Doctor::create([
                'user_id' => $user->id,
                'specialization' => $data['specialization'],
                'queue_prefix' => $data['queue_prefix'],
                'is_active' => $data['is_active'] ?? true,
            ]);
        });

        $doctor->load('user');

        return response()->json([
            'message' => 'Doctor created',
            'data' => new DoctorResource($doctor),
            'doctor' => new DoctorResource($doctor),
        ], 201);
    }

    public function show(Doctor $doctor): JsonResponse
    {
        $doctor->load('user');

        return response()->json([
            'data' => new DoctorResource($doctor),
            'doctor' => new DoctorResource($doctor),
        ]);
    }

    public function update(UpdateDoctorRequest $request, Doctor $doctor): JsonResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $doctor) {
            $userData = [];
            if (isset($data['name'])) {
                $userData['name'] = $data['name'];
            }
            if (isset($data['email'])) {
                $userData['email'] = $data['email'];
            }
            if (array_key_exists('phone', $data)) {
                $userData['phone'] = $data['phone'];
            }
            if (isset($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }

            if (! empty($userData)) {
                $doctor->user->update($userData);
            }

            $doctorData = [];
            if (isset($data['specialization'])) {
                $doctorData['specialization'] = $data['specialization'];
            }
            if (isset($data['queue_prefix'])) {
                $doctorData['queue_prefix'] = $data['queue_prefix'];
            }
            if (isset($data['is_active'])) {
                $doctorData['is_active'] = $data['is_active'];
            }

            if (! empty($doctorData)) {
                $doctor->update($doctorData);
            }
        });

        $doctor->load('user');

        return response()->json([
            'message' => 'Doctor updated',
            'data' => new DoctorResource($doctor),
            'doctor' => new DoctorResource($doctor),
        ]);
    }

    public function destroy(Doctor $doctor): JsonResponse
    {
        $doctor->delete();

        return response()->json([
            'message' => 'Doctor deleted',
        ]);
    }

    public function summary(Doctor $doctor, QueueService $queueService): JsonResponse
    {
        $summary = $queueService->getDoctorQueueSummary($doctor);

        return response()->json([
            'doctor' => new DoctorResource($doctor->load('user')),
            'summary' => [
                'total' => $summary['total'],
                'waiting' => $summary['waiting'],
                'called' => $summary['called'],
                'serving' => $summary['serving'],
                'done' => $summary['done'],
                'skipped' => $summary['skipped'],
            ],
            'current_queue' => $summary['current']
                ? new QueueResource($summary['current'])
                : null,
        ]);
    }
}
