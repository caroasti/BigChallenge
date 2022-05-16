<?php

namespace Tests\Feature;

use App\Models\DoctorInformation;
use App\Models\PatientInformation;
use App\Models\Submission;
use App\Models\User;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GetDoctorInformationTest extends TestCase
{
    use RefreshDatabase;

    public function test_doctor_can_get_his_her_information()
    {
        $user = User::factory()->doctor()->patient()->create();
        Sanctum::actingAs($user);
        $response = $this->getJson('/api/getDoctorInformation/1');
        $response->assertSuccessful();
        $response->assertJson(['message' => 'Received Doctor Information successfully',
                                'data' =>  ['speciality' => $user->doctorInformation->speciality,
                                            'grade' => $user->doctorInformation->grade, ],
                                ]);
    }

    public function test_patient_can_get_his_her_doctor_information()
    {
        $user = User::factory()->patient()->create();
        Sanctum::actingAs($user);

        $submission1 = Submission::factory()->inProgress()->create(['patient_id' => $user->id,]);
        Submission::factory()->inProgress()->create();

        $response = $this->getJson('/api/getDoctorInformation/1');
        $response->assertSuccessful();
        $response->assertJson(['message' => 'Received Doctor Information successfully',
                                'data' =>  ['speciality' => $submission1->doctor->doctorInformation->speciality,
                                            'grade' => $submission1->doctor->doctorInformation->grade, ],
                                ]);
    }

    public function test_patient_not_authorized_to_get_other_doctor_information()
    {
        $user = User::factory()->patient()->create();
        Sanctum::actingAs($user);
        Submission::factory()->inProgress()->create();
        $response = $this->getJson('/api/getDoctorInformation/1');
        $response->assertStatus(403);
    }
}
