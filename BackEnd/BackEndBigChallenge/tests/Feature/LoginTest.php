<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_log_in_successfully()
    {
        $user = User::factory()->create(['password' => Hash::make('pablooo')]);
        $response = $this->postJson('/api/login', ['email'=> $user->email, 'password'=>'pablooo']);
        $response->assertSuccessful();
        $response->assertJson([
            'status' => 200,
            'message' => 'User logged succesfully', ]);
    }

    /**
     * @dataProvider wrongCredentialsProvider
     */
    public function test_log_in_wrong_credentials($user)
    {
        User::factory()->create(['email'=>'pablo@pablo.com', 'password'=> 'passwordPablo']);
        $response = $this->postJson('/api/login', $user);
        $response->assertJson(['message' => 'The provided credentials are incorrect.']);
    }

    public function wrongCredentialsProvider(): array
    {
        return [
            ['mustFailPassword' => ['email'=>'pablo@pablo.com', 'password'=> 'wrongPassword']],
            ['mustFailEmail' => ['email'=>'notanemail@pablo.com', 'password'=> 'passwordPablo']],
            ['mustFailBoth' => ['email'=>'notanemail@pablo.com', 'password'=> 'wrongPassword']],
        ];
    }

    /**
     * @dataProvider invalidCredentialsProvider
     */
    public function test_log_in_invalid_credentials($user)
    {
        User::factory()->create(['email'=>'pablo@pablo.com', 'password'=> 'passwordPablo']);
        $response = $this->postJson('/api/login', $user);
        $response->assertStatus(422);
    }

    public function invalidCredentialsProvider(): array
    {
        return [
            ['mustFailPassword' => ['email'=>'valid@fd.fds', 'password'=> 'short']],
            ['mustFailEmail' => ['email'=>'notanemail@pablo.com', 'password'=> 'passwordPablo']],
            ['mustFailBoth' => ['email'=>'notAValidEmail@.', 'password'=> 'a']],
        ];
    }

    /**
     * @dataProvider usersProvider
     */
    public function test_log_in_if_already_logged_in($user)
    {
        Sanctum::actingAs(User::factory()->create());
        $response = $this->postJson('/api/login', $user);
        // SAME DOUBT HERE (Do I need to do anything more in this test?)
        $response->assertStatus(302);
    }

    public function usersProvider(): array
    {
        return [
            ['mustFailPassword' => ['email'=>'valid@fd.fds', 'password'=> 'short']],
            ['mustFailEmail' => ['email'=>'notanemail@pablo.com', 'password'=> 'passwordPablo']],
            ['mustFailBoth' => ['email'=>'notAValidEmail@.', 'password'=> 'a']],
            ['mustFailBoth' => ['email'=>'', 'password'=> '']],
        ];
    }
}
