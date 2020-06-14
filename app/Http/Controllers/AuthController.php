<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuthResource;
use App\User;
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

        return $this->authenticated($request, $user);
    }

    public function signIn(Request $request)
    {
        $this->validateLogin($request);
        if ($this->attemptLogin($request)) {
            return $this->authenticated($request, auth()->user());
        }

        return $this->sendFailedLoginResponse($request);
    }

    public function user(Request $request)
    {
        $user = auth()->user();

        return new AuthResource($user);
    }

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
