<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesSeeder;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_patient_succesfully()
    {
        (new RolesSeeder)->run();
        Notification::fake();
        $response = $this->postJson('/api/register', [
            'name' => 'pablito',
            'email' => 'caroasti@hotmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'patient',
            'gender' => 'male',
            'height' => '170',
            'weight' => '74',
            'birth' => '2000-12-06',
            'diseases' => 'diabethes',
            'previous_treatments' => 't4',
            'grade' => null,
            'speciality' => null,
        ]);
        $response->assertSuccessful();
        $this->assertDatabaseHas('users', ['email' => 'caroasti@hotmail.com']);
        $this->assertDatabaseHas('patient_information', ['height' => '170', 'weight' => '74']);
        $this->assertDatabaseMissing('doctor_information', []);
        Notification::assertSentTo([User::first()], VerifyEmail::class);
        $response->assertJson([
            'status' => 200,
            'message' => 'User registered succesfully',
        ]);
    }

    public function test_register_doctor_succesfully()
    {
        (new RolesSeeder)->run();
        Notification::fake();
        $response = $this->postJson('/api/register', [
            'name' => 'pablito',
            'email' => 'caroasti@hotmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'doctor',
            'gender' => 'male',
            'height' => '170',
            'weight' => '74',
            'birth' => '2000-12-06',
            'diseases' => 'diabethes',
            'previous_treatments' => 't4',
            'grade' => 2,
            'speciality' => 'Cardiology',
        ]);
        $response->assertSuccessful();
        $this->assertDatabaseHas('users', ['email' => 'caroasti@hotmail.com']);
        $this->assertDatabaseHas('patient_information', ['height' => '170', 'weight' => '74']);
        $this->assertDatabaseHas('doctor_information', ['grade' => 2, 'speciality' => 'Cardiology']);
        Notification::assertSentTo([User::first()], VerifyEmail::class);
        $response->assertJson([
            'status' => 200,
            'message' => 'User registered succesfully',
        ]);
    }

    /**
     * @dataProvider validUsersCredentialsProvider
     */
    public function test_register_two_times($user)
    {
        (new RolesSeeder)->run();
        $this->postJson('/api/register', $user);
        $response2 = $this->postJson('/api/register', $user);
        $response2->assertStatus(422);
    }

    public function validUsersCredentialsProvider(): array
    {
        return [
            ['validDoctor' => [
                'name' => 'pablito',
                'email' => 'caroasti@hotmail.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'role' => 'doctor',
                'gender' => 'male',
                'height' => '170',
                'weight' => '74',
                'birth' => '2000-12-06',
                'diseases' => 'diabethes',
                'previous_treatments' => 't4',
                'grade' => 2,
                'speciality' => 'Cardiology',
            ]],
            ['validPatient' => [
                'name' => 'pablito',
                'email' => 'caroasti@hotmail.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'role' => 'patient',
                'gender' => 'male',
                'height' => '170',
                'weight' => '74',
                'birth' => '2000-12-06',
                'diseases' => 'diabethes',
                'previous_treatments' => 't4',
                'grade' => null,
                'speciality' => null,
            ]],
        ];
    }

    public function test_register_if_already_logged()
    {
        Sanctum::actingAs(User::factory()->create());
        $response = $this->postJson('/api/register');
        $response->assertStatus(302);
    }

    /**
     * @dataProvider invalidCredentialsProvider
     */
    public function test_invalid_credentials($user)
    {
        Notification::fake();
        $response = $this->postJson('/api/register', $user);
        Notification::assertNothingSent();
        $response->assertStatus(422);
    }

    public function invalidCredentialsProvider(): array
    {
        return [

            ['noName' => [
                'email' => 'caroasti@hotmail.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'role' => 'doctor',
                'gender' => 'male',
                'height' => '170',
                'weight' => '74',
                'birth' => '2000-12-06',
                'diseases' => 'diabethes',
                'previous_treatments' => 't4',
                'grade' => 2,
                'speciality' => 'Cardiology',
            ]],
            ['noPass' => [
                'name' => 'pablito',
                'email' => 'caroasti@hotmail.com',
                'password_confirmation' => 'password',
                'role' => 'doctor',
                'gender' => 'male',
                'height' => '170',
                'weight' => '74',
                'birth' => '2000-12-06',
                'diseases' => 'diabethes',
                'previous_treatments' => 't4',
                'grade' => 2,
                'speciality' => 'Cardiology',
            ]],
            ['noPassConfirmation' => [
                'name' => 'pablito',
                'email' => 'caroasti@hotmail.com',
                'password' => 'password',
                'role' => 'doctor',
                'gender' => 'male',
                'height' => '170',
                'weight' => '74',
                'birth' => '2000-12-06',
                'diseases' => 'diabethes',
                'previous_treatments' => 't4',
                'grade' => 2,
                'speciality' => 'Cardiology',
            ]],
            ['wrongPassConfirmation' => [
                'name' => 'pablito',
                'email' => 'caroasti@hotmail.com',
                'password' => 'password',
                'password_confirmation' => 'wronggggpassword',
                'role' => 'doctor',
                'gender' => 'male',
                'height' => '170',
                'weight' => '74',
                'birth' => '2000-12-06',
                'diseases' => 'diabethes',
                'previous_treatments' => 't4',
                'grade' => 2,
                'speciality' => 'Cardiology',
            ]],
            ['shortPass' => [
                'name' => 'pablito',
                'email' => 'caroasti@hotmail.com',
                'password' => 'short',
                'password_confirmation' => 'short',
                'role' => 'doctor',
                'gender' => 'male',
                'height' => '170',
                'weight' => '74',
                'birth' => '2000-12-06',
                'diseases' => 'diabethes',
                'previous_treatments' => 't4',
                'grade' => 2,
                'speciality' => 'Cardiology',
            ]],
            ['noRole' => [
                'name' => 'pablito',
                'email' => 'caroasti@hotmail.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'gender' => 'male',
                'height' => '170',
                'weight' => '74',
                'birth' => '2000-12-06',
                'diseases' => 'diabethes',
                'previous_treatments' => 't4',
                'grade' => 2,
                'speciality' => 'Cardiology',
            ]],
            ['noGender' => [
                'name' => 'pablito',
                'email' => 'caroasti@hotmail.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'role' => 'doctor',
                'height' => '170',
                'weight' => '74',
                'birth' => '2000-12-06',
                'diseases' => 'diabethes',
                'previous_treatments' => 't4',
                'grade' => 2,
                'speciality' => 'Cardiology',
            ]],
            ['noHeight' => [
                'name' => 'pablito',
                'email' => 'caroasti@hotmail.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'role' => 'doctor',
                'gender' => 'male',
                'weight' => '74',
                'birth' => '2000-12-06',
                'diseases' => 'diabethes',
                'previous_treatments' => 't4',
                'grade' => 2,
                'speciality' => 'Cardiology',
            ]],
            ['noWeight' => [
                'name' => 'pablito',
                'email' => 'caroasti@hotmail.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'role' => 'doctor',
                'gender' => 'male',
                'height' => '170',
                'birth' => '2000-12-06',
                'diseases' => 'diabethes',
                'previous_treatments' => 't4',
                'grade' => 2,
                'speciality' => 'Cardiology',
            ]],
            ['noBirth' => [
                'name' => 'pablito',
                'email' => 'caroasti@hotmail.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'role' => 'doctor',
                'gender' => 'male',
                'height' => '170',
                'weight' => '74',
                'diseases' => 'diabethes',
                'previous_treatments' => 't4',
                'grade' => 2,
                'speciality' => 'Cardiology',
            ]],
            ['noEmail' => [
                'name' => 'pablito',
                'password' => 'password',
                'password_confirmation' => 'password',
                'role' => 'doctor',
                'gender' => 'male',
                'height' => '170',
                'weight' => '74',
                'birth' => '2000-12-06',
                'diseases' => 'diabethes',
                'previous_treatments' => 't4',
                'grade' => 2,
                'speciality' => 'Cardiology',
            ]],
            ['invalidEmail' => [
                'name' => 'pablito',
                'email' => 'pablitopaloutdmgmail.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'role' => 'doctor',
                'gender' => 'male',
                'height' => '170',
                'weight' => '74',
                'birth' => '2000-12-06',
                'diseases' => 'diabethes',
                'previous_treatments' => 't4',
                'grade' => 2,
                'speciality' => 'Cardiology',
            ]],
            ['noGrade' => [
                'name' => 'pablito',
                'email' => 'caroasti@hotmail.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'role' => 'doctor',
                'gender' => 'male',
                'height' => '170',
                'weight' => '74',
                'birth' => '2000-12-06',
                'diseases' => 'diabethes',
                'previous_treatments' => 't4',
                'speciality' => 'Cardiology',
            ]],
            ['gradeGreaterThan5' => [
                'name' => 'pablito',
                'email' => 'caroasti@hotmail.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'role' => 'doctor',
                'gender' => 'male',
                'height' => '170',
                'weight' => '74',
                'birth' => '2000-12-06',
                'diseases' => 'diabethes',
                'grade' => 6,
                'previous_treatments' => 't4',
                'speciality' => 'Cardiology',
            ]],
            ['noSpeciality' => [
                'name' => 'pablito',
                'email' => 'caroasti@hotmail.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'role' => 'doctor',
                'gender' => 'male',
                'height' => '170',
                'weight' => '74',
                'birth' => '2000-12-06',
                'diseases' => 'diabethes',
                'previous_treatments' => 't4',
                'grade' => 2,
            ]],
        ];
    }
}
