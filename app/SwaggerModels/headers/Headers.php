<?php


/**
 * @OA\Header(
 *     header="Access-Token",
 *     description="Токен",
 *     @OA\Schema( type="string" )
 * ),
 * @OA\Header(
 *     header="Refresh-token",
 *     description="Токен для обновление access токена",
 *     @OA\Schema( type="string" )
 * ),
 * @OA\Header(
 *     header="Token-Type",
 *     description="Тип токена",
 *     @OA\Schema( type="string" )
 * ),
 * @OA\Header(
 *     header="Expires-In",
 *     description="продолжительность жизни токена",
 *     @OA\Schema( type="int" )
 * ),
 */
class Headers {
}
