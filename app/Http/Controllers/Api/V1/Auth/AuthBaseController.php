<?php
namespace App\Http\Controllers\Api\V1\Auth;

/**
 * @OA\Tag(
 *     name="Authentication",
 *     description="Authentication",
 * )
 */

use App\Http\Controllers\Api\Controller;
use App\Http\Resources\AuthResource;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use GuzzleHttp\Psr7\ServerRequest as GuzzleRequest;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use League\OAuth2\Server\AuthorizationServer;
use Exception;
use Laravel\Passport\Client as OClient;

class AuthBaseController extends Controller
{
    use AuthenticatesUsers;

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

    protected function getTokenAndRefreshToken($email, $password) {
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


    protected function generateRefreshToken(Request $request)
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
