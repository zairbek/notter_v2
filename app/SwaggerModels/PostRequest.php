<?php


/**
 * @OA\Schema(
 *     description="Sign-in request",
 *     type="object",
 *     title="Sign in request"
 * )
 */
class PostRequest
{
    /**
     * @OA\Property(
     *     description="Email",
     *     title="Email",
     *     format="string",
     *     example="test@test.ru",
     * )
     *
     * @var int
     */
    private $email;

    /**
     * @OA\Property(
     *    description="password",
     *    title="Password",
     *    format="string",
     *    example="123456",
     * )
     *
     * @var string
     */
    private $password;
}
