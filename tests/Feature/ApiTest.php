<?php

use App\Enums\QueueStatus;
use App\Enums\UserRole;
use App\Models\Doctor;
use App\Models\Queue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can register', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'Jane Patient',
        'email' => 'jane@patient.test',
        'password' => 'password',
        'password_confirmation' => 'password',
        'phone' => '08123456789',
    ]);

    $response->assertCreated()
        ->assertJsonStructure(['message', 'user', 'token']);

    $this->assertDatabaseHas('users', [
        'email' => 'jane@patient.test',
        'role' => UserRole::Patient->value,
    ]);
});

test('user can login', function () {
    $user = User::factory()->patient()->create([
        'email' => 'jane@patient.test',
        'password' => bcrypt('password'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'jane@patient.test',
        'password' => 'password',
    ]);

    $response->assertOk()
        ->assertJsonStructure(['message', 'user', 'token']);
});

test('guest can list active doctors', function () {
    $activeUser = User::factory()->doctor()->create();
    $inactiveUser = User::factory()->doctor()->create();

    Doctor::factory()->create([
        'user_id' => $activeUser->id,
        'is_active' => true,
    ]);

    Doctor::factory()->create([
        'user_id' => $inactiveUser->id,
        'is_active' => false,
    ]);

    $response = $this->getJson('/api/doctors');

    $response->assertOk()
        ->assertJsonCount(1, 'doctors');
});

test('admin can create and manage doctors', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)
        ->postJson('/api/doctors', [
            'name' => 'Dr. House',
            'email' => 'house@smaps.test',
            'password' => 'password',
            'phone' => '123456',
            'specialization' => 'Diagnostic',
            'queue_prefix' => 'H',
        ]);

    $response->assertCreated();
    $this->assertDatabaseHas('users', ['email' => 'house@smaps.test']);
    $this->assertDatabaseHas('doctors', ['specialization' => 'Diagnostic']);

    $doctor = Doctor::first();

    $response = $this->actingAs($admin)
        ->putJson("/api/doctors/{$doctor->id}", [
            'specialization' => 'Nephrology',
            'is_active' => false,
        ]);

    $response->assertOk();
    $this->assertDatabaseHas('doctors', [
        'id' => $doctor->id,
        'specialization' => 'Nephrology',
        'is_active' => false,
    ]);
});

test('patient can create queue and check it', function () {
    $patient = User::factory()->patient()->create();
    $doctorUser = User::factory()->doctor()->create();
    $doctor = Doctor::factory()->create([
        'user_id' => $doctorUser->id,
        'queue_prefix' => 'A',
    ]);

    $response = $this->actingAs($patient)
        ->postJson('/api/queues', [
            'doctor_id' => $doctor->id,
            'patient_name' => 'Self',
        ]);

    $response->assertCreated()
        ->assertJsonPath('queue.queue_number', 'A-1');

    $response = $this->getJson('/api/check-queue?queue_number=A-1');
    $response->assertOk()
        ->assertJsonPath('queue.queue_number', 'A-1');
});

test('doctor/admin can call next, serve, and complete queues', function () {
    $admin = User::factory()->admin()->create();
    $doctorUser = User::factory()->doctor()->create();
    $doctor = Doctor::factory()->create([
        'user_id' => $doctorUser->id,
        'queue_prefix' => 'T',
    ]);

    // Create 2 queues
    Queue::factory()->create([
        'doctor_id' => $doctor->id,
        'queue_number' => 'T-1',
        'status' => QueueStatus::Waiting,
        'queue_date' => now()->toDateString(),
    ]);

    $queue2 = Queue::factory()->create([
        'doctor_id' => $doctor->id,
        'queue_number' => 'T-2',
        'status' => QueueStatus::Waiting,
        'queue_date' => now()->toDateString(),
    ]);

    // Call Next
    $response = $this->actingAs($admin)
        ->postJson("/api/doctors/{$doctor->id}/call-next");

    $response->assertOk()
        ->assertJsonPath('queue.queue_number', 'T-1')
        ->assertJsonPath('queue.status', 'called');

    $queue1 = Queue::where('queue_number', 'T-1')->first();

    // Serve
    $response = $this->actingAs($admin)
        ->postJson("/api/queues/{$queue1->id}/serve");

    $response->assertOk()
        ->assertJsonPath('queue.status', 'serving');

    // Complete
    $response = $this->actingAs($admin)
        ->postJson("/api/queues/{$queue1->id}/complete");

    $response->assertOk()
        ->assertJsonPath('queue.status', 'done');
});
