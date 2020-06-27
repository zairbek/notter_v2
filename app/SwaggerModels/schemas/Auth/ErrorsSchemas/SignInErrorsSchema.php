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
 *                 example="These credentials do not match our records.",
 *             ),
 *         ),
 *     ),
 * )
 */
class SignInErrorsSchema {}
