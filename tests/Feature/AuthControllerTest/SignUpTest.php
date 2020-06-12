<?php


namespace Tests\Feature;


use App\User;
use Tests\TestCase;

class SignUpTest extends TestCase
{
    /**
     * @test
     */
    public function success()
    {
        $response = $this->postJson(route('auth.sign-up'), [
            'email' => 'test@mail.ru',
            'name' => 'Zair',
            'password' => '123456',
            'password_confirmation' => '123456',
        ]);

        $response->assertSuccessful();
        $user = User::whereEmail('test@mail.ru')->first();
        $this->assertNotNull($user);
        $this->assertEquals($user->id, $response->json('data.user.id'));
        $this->assertEquals($user->name, $response->json('data.user.name'));
        $response->assertHeader('access-token');
        $response->assertHeader('refresh-token');
        $response->assertHeader('token-type');
        $response->assertHeader('expires-in');
    }

    /**
     * @test
     * @dataProvider provider_validation
     */
    public function validation($params, $errors)
    {
        $this
            ->postJson(route('auth.sign-up', $params))
            ->assertJsonValidationErrors($errors)
        ;
    }

    public function provider_validation()
    {
        return [
            'Отсуствует емаил' => [['name' => 'вася', 'password' => '12345678', 'password_confirmation' => '12121212'], ['email']],
            'Отсуствует name' => [['email' => 'test@mail.ru', 'password' => '12345678', 'password_confirmation' => '12121212'], ['name']],
            'Отсуствует password' => [['name' => 'вася', 'email' => 'test@mail.ru', 'password_confirmation' => '12121212'], ['password']],
            'Пароли не совпадают' => [['name' => 'вася', 'password' => '12121212', 'email' => 'test@mail.ru'], ['password']],
        ];
    }

}
