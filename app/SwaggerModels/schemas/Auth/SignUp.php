<?php

/**
 * @OA\Schema(
 *     title="Sign up model"
 * )
 */
class SignUp
{
    /**
     * @OA\Property(
     *     type="string",
     *     example="Иван Иванов",
     * )
     */
    public $name;

    /**
     * @OA\Property(
     *     type="string",
     *     example="test@test.ru"
     * )
     */
    public $email;

    /**
     * @OA\Property(
     *     type="string",
     *     example="123456"
     * )
     */
    public $password;

    /**
     * @OA\Property(
     *     type="string",
     *     example="123456"
     * )
     */
    public $password_confirmation;
}
