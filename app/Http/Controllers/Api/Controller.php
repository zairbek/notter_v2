<?php

namespace App\Http\Controllers\Api;
/**
 * @OA\Info(
 *     title="Sansara API Dacumentation",
 *     version="1.0",
 * )
 * @OA\Server(
 *     description="Sansara Dev Server",
 *     url="http://notter.loc/api/v1",
 * )
 * @OA\Server(
 *     description="Sansara Prod Server",
 *     url="http://localhost-prod/api/v1",
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     in="header",
 *     scheme="Bearer",
 *     bearerFormat="JWT",
 *     name="bearerAuth",
 * )
 *
 */
class Controller extends \App\Http\Controllers\Controller
{
}
