<?php


namespace Tests\Unit;


use App\User;
use Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * @test
     */
    public function set_password_attribute()
    {
        $user = factory(User::class)->create();
        $user->password = '121212';

        $this->assertTrue(Hash::check('121212', $user->password));
    }
}
