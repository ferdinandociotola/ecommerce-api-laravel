<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_register()
    {
        $response=$this->postJson('/api/register', [
                'name'=> 'Test User',
                'email'=> 'test@example.com',
                'password'=> 'password',
                'password_confirmation'=>'password'
                ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                    'success',
                    'user'=>['id', 'name', 'email'],
                    'token'
                    ]);

        $this->assertDatabaseHas('users', [
                'email'=> 'test@example.com'
                ]);

    }

    /**@test */
    public function user_can_login()
    {
        $user=User::factory()->create([
                'email'=> 'test@example.com',
                'password'=> bcrypt('password')
                ]);

        $response =$this->postJson('/api/login', [
                'email'=> 'test@example.com',
                'password'=> 'password'
                ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                        'success',
                        'user',
                        'token'
                ]);
    }


    /** @test */
    public function login_fails_with_wrong_password()
    {
        User::factory()->create([
                'email'=> 'test@example.com',
                'password'=> bcrypt('password')
                ]);

        $response=$this->postJson('/api/login', [
                'email'=> 'test@example.com',
                'password'=> 'wrong-password'
                ]);


        $response->assertStatus(401)
                    ->assertJson([
                        'message'=> 'Credenziali non valide'
                    ]);
    }




}
