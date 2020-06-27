<?php

namespace App\Http\Controllers\Api;
/**
 * @OA\Info(
 *     title="Sansara API Dacumentation",
 *     version="1.0",
 * )
 * @OA\Tag(
 *     name="Authentication",
 *     description="Authentication",
 * )
 * @OA\Server(
 *     description="Sansara Dev Server",
 *     url="http://localhost:8888/api",
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
