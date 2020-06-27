<?php
namespace App\Http\Controllers\Api\V1\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RefreshTokenController extends AuthBaseController
{
    /**
     * @OA\Post(
     *     path="/auth/refresh-token",
     *     operationId="refreshToken",
     *     tags={"Authentication"},
     *     summary="Refresh access token & refresh token",
     *     @OA\Parameter(
     *         in="header",
     *         name="refresh-token",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\Header(header="Access-Token", ref="#/components/headers/Access-Token"),
     *         @OA\Header(header="Refresh-token", ref="#/components/headers/Refresh-token"),
     *         @OA\Header(header="Token-Type", ref="#/components/headers/Token-Type"),
     *         @OA\Header(header="Expires-In", ref="#/components/headers/Expires-In"),
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Refresh token invalid",
     *     ),
     * )
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|Response
     */
    public function refreshToken(Request $request)
    {
        $token = $this->generateRefreshToken($request);
        if (!$token) {
            return response()->json('Refresh token invalid', Response::HTTP_FORBIDDEN);
        }
        return response([])
            ->header('access-token', $token['access_token'])
            ->header('refresh-token', $token['refresh_token'])
            ->header('token-type', $token['token_type'])
            ->header('expires-in', $token['expires_in'])
            ;
    }

}
