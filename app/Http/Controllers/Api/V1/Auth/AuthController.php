<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthResource;
use App\Notifications\VerifyEamilNotification;
use App\User;
use Carbon\Carbon;
use Exception;
use Firebase\JWT\ExpiredException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Passport\Client as OClient;
use GuzzleHttp\Psr7\ServerRequest as GuzzleRequest;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use League\OAuth2\Server\AuthorizationServer;
use Illuminate\Support\Facades\Auth;
use const Response;


class AuthController extends Controller
{
    use AuthenticatesUsers;


    /**
     * @OA\Post(
     *     path="/auth/sign-up",
     *     tags={"Authentication"},
     *     summary="Регистрация",
     *     operationId="signUp",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/SignUp")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(ref="#/components/schemas/AuthResponse"),
     *         @OA\Header(header="Access-Token", ref="#/components/headers/Access-Token"),
     *         @OA\Header(header="Refresh-token", ref="#/components/headers/Refresh-token"),
     *         @OA\Header(header="Token-Type", ref="#/components/headers/Token-Type"),
     *         @OA\Header(header="Expires-In", ref="#/components/headers/Expires-In"),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity",
     *         @OA\JsonContent(ref="#/components/schemas/SignUpErrorsSchema"),
     *     ),
     * )
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function signUp(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|unique:users',
            'name' => 'required',
            'password' => 'required|confirmed'
        ]);

        $user = new User();
        $user->email = $request->email;
        $user->name = $request->name;
        $user->password = $request->password;
        $user->save();

        $user->notify(new VerifyEamilNotification($user));

        return $this->authenticated($request, $user);
    }

    /**
     * @OA\Post(
     *     path="/auth/sign-in",
     *     tags={"Authentication"},
     *     summary="Авторизация",
     *     operationId="signIn",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(ref="#/components/schemas/SignIn")
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Ok",
     *         @OA\JsonContent(ref="#/components/schemas/AuthResponse"),
     *         @OA\Header(header="Access-Token", ref="#/components/headers/Access-Token"),
     *         @OA\Header(header="Refresh-token", ref="#/components/headers/Refresh-token"),
     *         @OA\Header(header="Token-Type", ref="#/components/headers/Token-Type"),
     *         @OA\Header(header="Expires-In", ref="#/components/headers/Expires-In"),
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity",
     *         @OA\JsonContent(ref="#/components/schemas/SignInErrorsSchema"),
     *     ),
     * )
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Illuminate\Validation\ValidationException
     */

    public function signIn(Request $request)
    {
        $this->validateLogin($request);
        if ($this->attemptLogin($request)) {
            return $this->authenticated($request, auth()->user());
        }

        return $this->sendFailedLoginResponse($request);
    }

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

    protected function authenticated(Request $request, $user)
    {
        $token = $this->getTokenAndRefreshToken($request->email, $request->password);

        return (new AuthResource($user))
            ->response()
            ->header('access-token', $token['access_token'])
            ->header('refresh-token', $token['refresh_token'])
            ->header('token-type', $token['token_type'])
            ->header('expires-in', $token['expires_in'])
            ;
    }

    public function verify(User $user)
    {
        $user->email_verified_at = Carbon::now();
        $user->save();
    }


    private function getTokenAndRefreshToken($email, $password) {
        /** @src https://qna.habr.com/qe/652418 */
        $oClient = OClient::where('password_client', 1)->first();
        $server = app(AuthorizationServer::class);
        $psrReponse = $server->respondToAccessTokenRequest((new GuzzleRequest('POST', ''))->withParsedBody([
            'grant_type' => 'password',
            'client_id' => $oClient->id,
            'client_secret' => $oClient->secret,
            'username' => $email,
            'password' => $password,
            'scope' => '*',
        ]), new GuzzleResponse());
        $token = json_decode((string) $psrReponse->getBody(), true);

        return $token;
    }


    private function generateRefreshToken(Request $request)
    {
        try {
            $refreshToken = $request->header('refresh-token');
            $oClient = OClient::where('password_client', 1)->first();
            $server = app(AuthorizationServer::class);
            $psrReponse = $server->respondToAccessTokenRequest((new GuzzleRequest('POST', ''))->withParsedBody([
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
                'client_id' => $oClient->id,
                'client_secret' => $oClient->secret,
                'scope' => '*',
            ]), new GuzzleResponse());
            $token = json_decode((string) $psrReponse->getBody(), true);
            return $token;
        } catch (Exception $e) {
            return false;
        }
    }


}
