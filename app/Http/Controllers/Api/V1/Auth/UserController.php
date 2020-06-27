<?php
namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Resources\AuthResource;
use Illuminate\Http\Request;

class UserController extends AuthBaseController
{
    /**
     * @OA\Get(
     *   path="/auth/user",
     *   operationId="user",
     *   tags={"Authentication"},
     *   summary="Возвращает текущего авторизованного пользователя",
     *   security={{"bearerAuth":{}}},
     *   @OA\Response(
     *     response=200,
     *     description="Ok",
     *     @OA\JsonContent(ref="#/components/schemas/AuthResponse"),
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Unauthorized",
     *     @OA\JsonContent(ref="#/components/schemas/UserUnauthenticatedSchema"),
     *   )
     * )
     *
     * @param Request $request
     * @return AuthResource
     */
    public function user(Request $request)
    {
        $user = auth()->user();

        return new AuthResource($user);
    }

}
