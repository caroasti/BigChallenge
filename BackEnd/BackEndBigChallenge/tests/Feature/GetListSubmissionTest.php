<?php

namespace Tests\Feature;

use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GetListSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_all_patient_submissions()
    {
        $userPatient = User::factory()->patient()->create();
        Submission::factory()->count(3)->create([
            'patient_id' => $userPatient->id,
        ]);
        Submission::factory()->count(7)->create();
        Sanctum::actingAs($userPatient);
        $response = $this->getJson('/api/submission');
        $response->assertSuccessful();
        $response->assertJsonCount(3, 'data');
    }

    public function test_get_all_patient_submissions_pending()
    {
        $userPatient = User::factory()->patient()->create();
        Submission::factory()->count(3)->create([
            'patient_id' => $userPatient->id,
        ]);
        Submission::factory()->inProgress()->count(4)->create([
            'patient_id' => $userPatient->id,
        ]);
        Submission::factory()->ready()->count(5)->create([
            'patient_id' => $userPatient->id,
        ]);
        Submission::factory()->count(15)->create();
        Sanctum::actingAs($userPatient);
        $response = $this->getJson('/api/submission?state=' . Submission::STATUS_PENDING);
        $response->assertSuccessful();
        $response->assertJsonCount(3, 'data');
    }

    public function test_get_all_patient_submissions_in_progress()
    {
        $userPatient = User::factory()->patient()->create();
        Submission::factory()->count(3)->create([
            'patient_id' => $userPatient->id,
        ]);
        Submission::factory()->inProgress()->count(4)->create([
            'patient_id' => $userPatient->id,
        ]);
        Submission::factory()->ready()->count(5)->create([
            'patient_id' => $userPatient->id,
        ]);
        Submission::factory()->count(15)->create();
        Sanctum::actingAs($userPatient);

        $response = $this->getJson('/api/submission?state=' . Submission::STATUS_IN_PROGRESS);
        $response->assertSuccessful();
        $response->assertJsonCount(4, 'data');
    }

    public function test_get_all_patient_submissions_ready()
    {
        $userPatient = User::factory()->patient()->create();
        Submission::factory()->count(3)->create([
            'patient_id' => $userPatient->id,
        ]);
        Submission::factory()->inProgress()->count(4)->create([
            'patient_id' => $userPatient->id,
        ]);
        Submission::factory()->ready()->count(5)->create([
            'patient_id' => $userPatient->id,
        ]);
        Submission::factory()->count(15)->create();
        Sanctum::actingAs($userPatient);

        $response = $this->getJson('/api/submission?state=' . Submission::STATUS_DONE);
        $response->assertSuccessful();
        $response->assertJsonCount(5, 'data');
    }

    public function test_get_doctor_submissions_ready()
    {
        $userDoctor = User::factory()->doctor()->patient()
                    ->has(Submission::factory()->count(3), 'submissionsMade')
                    ->has(Submission::factory()->inProgress()->count(7), 'submissionsMade')
                    ->has(Submission::factory()->ready()->count(10), 'submissionsMade')
                    ->has(Submission::factory()->inProgress()->count(4), 'submissionsTaken')
                    ->has(Submission::factory()->ready()->count(5), 'submissionsTaken')
                    ->create();
        Submission::factory()->count(10)->create();
        Sanctum::actingAs($userDoctor);

        $response = $this->getJson('/api/submission?role=doctor&state=' . Submission::STATUS_DONE);
        $response->assertSuccessful();
        $response->assertJsonCount(5, 'data');
    }

    public function test_get_doctor_submissions_in_progress()
    {
        $userDoctor = User::factory()->doctor()->patient()
                    ->has(Submission::factory()->count(3), 'submissionsMade')
                    ->has(Submission::factory()->inProgress()->count(7), 'submissionsMade')
                    ->has(Submission::factory()->ready()->count(10), 'submissionsMade')
                    ->has(Submission::factory()->inProgress()->count(4), 'submissionsTaken')
                    ->has(Submission::factory()->ready()->count(5), 'submissionsTaken')
                    ->create();
        Submission::factory()->count(10)->create();
        Sanctum::actingAs($userDoctor);

        $response = $this->getJson('/api/submission?role=doctor&state=' . Submission::STATUS_IN_PROGRESS);
        $response->assertSuccessful();
        $response->assertJsonCount(4, 'data');
    }

    public function test_get_all_doctor_submissions()
    {
        $userDoctor = User::factory()->doctor()->patient()
                    ->has(Submission::factory()->count(3), 'submissionsMade')
                    ->has(Submission::factory()->inProgress()->count(7), 'submissionsMade')
                    ->has(Submission::factory()->ready()->count(10), 'submissionsMade')
                    ->has(Submission::factory()->inProgress()->count(4), 'submissionsTaken')
                    ->has(Submission::factory()->ready()->count(5), 'submissionsTaken')
                    ->create();
        Submission::factory()->count(10)->create();
        Sanctum::actingAs($userDoctor);

        $response = $this->getJson('/api/submission?role=doctor');
        $response->assertSuccessful();
        $response->assertJsonCount(9, 'data');
    }

    public function test_get_doctor_submissions_as_patient()
    {
        $userDoctor = User::factory()->doctor()->patient()
                    ->has(Submission::factory()->count(3), 'submissionsMade')
                    ->has(Submission::factory()->inProgress()->count(7), 'submissionsMade')
                    ->has(Submission::factory()->ready()->count(10), 'submissionsMade')
                    ->has(Submission::factory()->inProgress()->count(4), 'submissionsTaken')
                    ->has(Submission::factory()->ready()->count(5), 'submissionsTaken')
                    ->create();
        Submission::factory()->count(10)->create();
        Sanctum::actingAs($userDoctor);

        $response = $this->getJson('/api/submission');
        $response->assertSuccessful();
        $response->assertJsonCount(20, 'data');
    }

    public function test_get_doctor_submissions_pending_as_patient()
    {
        $userDoctor = User::factory()->doctor()->patient()
                    ->has(Submission::factory()->count(3), 'submissionsMade')
                    ->has(Submission::factory()->inProgress()->count(7), 'submissionsMade')
                    ->has(Submission::factory()->ready()->count(10), 'submissionsMade')
                    ->has(Submission::factory()->inProgress()->count(4), 'submissionsTaken')
                    ->has(Submission::factory()->ready()->count(5), 'submissionsTaken')
                    ->create();
        Submission::factory()->count(10)->create();
        Sanctum::actingAs($userDoctor);

        $response = $this->getJson('/api/submission?state=' . Submission::STATUS_PENDING);
        $response->assertSuccessful();
        $response->assertJsonCount(3, 'data');
    }

    public function test_get_doctor_submissions_in_progress_as_patient()
    {
        $userDoctor = User::factory()->doctor()->patient()
                    ->has(Submission::factory()->count(3), 'submissionsMade')
                    ->has(Submission::factory()->inProgress()->count(7), 'submissionsMade')
                    ->has(Submission::factory()->ready()->count(10), 'submissionsMade')
                    ->has(Submission::factory()->inProgress()->count(4), 'submissionsTaken')
                    ->has(Submission::factory()->ready()->count(5), 'submissionsTaken')
                    ->create();
        Submission::factory()->count(10)->create();
        Sanctum::actingAs($userDoctor);

        $response = $this->getJson('/api/submission?state=' . Submission::STATUS_IN_PROGRESS);
        $response->assertSuccessful();
        $response->assertJsonCount(7, 'data');
    }

    public function test_get_doctor_submissions_ready_as_patient()
    {
        $userDoctor = User::factory()->doctor()->patient()
                    ->has(Submission::factory()->count(3), 'submissionsMade')
                    ->has(Submission::factory()->inProgress()->count(7), 'submissionsMade')
                    ->has(Submission::factory()->ready()->count(10), 'submissionsMade')
                    ->has(Submission::factory()->inProgress()->count(4), 'submissionsTaken')
                    ->has(Submission::factory()->ready()->count(5), 'submissionsTaken')
                    ->create();
        Submission::factory()->count(10)->create();
        Sanctum::actingAs($userDoctor);

        $response = $this->getJson('/api/submission?state=' . Submission::STATUS_DONE);
        $response->assertSuccessful();
        $response->assertJsonCount(10, 'data');
    }

    public function test_get_submissions_by_guest()
    {
        Submission::factory()->count(7)->create();

        $response = $this->getJson('/api/submission');
        $response->assertStatus(401);
    }
}
