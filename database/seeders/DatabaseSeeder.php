<?php

namespace Database\Seeders;

use App\Enums\QueueStatus;
use App\Models\Doctor;
use App\Models\Queue;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin
        User::factory()->admin()->create([
            'name' => 'Admin SMAPS',
            'email' => 'admin@smaps.test',
        ]);

        // Doctors
        $doctorConfigs = [
            ['name' => 'Dr. Andi Pratama', 'specialization' => 'Umum', 'prefix' => 'A', 'email' => 'dr.andi@smaps.test'],
            ['name' => 'Dr. Siti Rahayu', 'specialization' => 'Gigi', 'prefix' => 'B', 'email' => 'dr.siti@smaps.test'],
            ['name' => 'Dr. Budi Santoso', 'specialization' => 'Anak', 'prefix' => 'C', 'email' => 'dr.budi@smaps.test'],
        ];

        foreach ($doctorConfigs as $config) {
            $user = User::factory()->doctor()->create([
                'name' => $config['name'],
                'email' => $config['email'],
            ]);

            $doctor = Doctor::create([
                'user_id' => $user->id,
                'specialization' => $config['specialization'],
                'queue_prefix' => $config['prefix'],
                'is_active' => true,
            ]);

            // Create sample queues for today
            $today = now()->toDateString();

            for ($i = 1; $i <= 5; $i++) {
                $status = match (true) {
                    $i <= 2 => QueueStatus::Done,
                    $i === 3 => QueueStatus::Serving,
                    default => QueueStatus::Waiting,
                };

                Queue::create([
                    'doctor_id' => $doctor->id,
                    'patient_name' => fake()->name(),
                    'patient_phone' => fake()->phoneNumber(),
                    'complaint' => fake()->sentence(),
                    'queue_number' => $config['prefix'].'-'.$i,
                    'status' => $status,
                    'queue_date' => $today,
                    'called_at' => $i <= 3 ? now()->subMinutes(60 - ($i * 15)) : null,
                    'served_at' => $i <= 3 ? now()->subMinutes(55 - ($i * 15)) : null,
                    'completed_at' => $i <= 2 ? now()->subMinutes(30 - ($i * 10)) : null,
                ]);
            }
        }

        // Demo Patient Budi
        User::factory()->patient()->create([
            'name' => 'Budi',
            'email' => 'budi@example.com',
        ]);

        // Extra patients
        User::factory(5)->create();
    }
}
