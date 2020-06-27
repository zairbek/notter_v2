<?php

/**
 * @OA\Schema(
 *     title="User schema",
 *     @OA\Property(
 *         property="user",
 *         type="object",
 *         @OA\Property(
 *             property="id",
 *             type="int",
 *             example="12",
 *         ),
 *         @OA\Property(
 *             property="name",
 *             type="string",
 *             example="Иван"
 *         ),
 *         @OA\Property(
 *             property="email",
 *             type="string",
 *             example="test@test.ru"
 *         )
 *     ),
 * )
 */
class UserSchema
{
}
