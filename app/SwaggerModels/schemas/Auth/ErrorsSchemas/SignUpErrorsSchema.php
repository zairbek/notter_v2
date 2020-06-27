<?php


/**
 * @OA\Schema(
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         example="The given data was invalid.",
 *     ),
 *     @OA\Property(
 *         property="errors",
 *         @OA\Property(
 *             property="email",
 *             type="array",
 *             @OA\Items(
 *                 type="string",
 *                 example="The email field is required",
 *             ),
 *         ),
 *         @OA\Property(
 *             property="name",
 *             type="array",
 *             @OA\Items(
 *                 type="string",
 *                 example="The name field is required.",
 *             ),
 *         ),
 *         @OA\Property(
 *             property="password",
 *             type="array",
 *             @OA\Items(
 *                 type="string",
 *                 example="The password field is required.",
 *             ),
 *         ),
 *     ),
 * )
 */
class SignUpErrorsSchema {
}
