<?php


namespace Tests\Feature;


use App\User;
use Firebase\JWT\JWT;
use Tests\TestCase;

class SignInTest extends TestCase
{
    /**
     * @var User
     */
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create([
            'password' => '123456',
        ]);
    }

    /**
     * @test
     */
    public function success()
    {
        $response = $this->postJson(route('auth.sign-in'), [
            'email' => $this->user->email,
            'password' => '123456'
        ]);

        $response->assertSuccessful();
    }

    /**
     * @test
     */
    public function not_allowed()
    {
        $response = $this->postJson(route('auth.sign-in'), [
            'email' => $this->user->email,
            'password' => '123123'
        ]);

        $response->assertJsonValidationErrors(['email']);
    }
}
