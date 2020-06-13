<?php


namespace Tests\Feature;


use App\User;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use GuzzleHttp\Psr7\ServerRequest as GuzzleRequest;
use Laravel\Passport\Client as OClient;
use League\OAuth2\Server\AuthorizationServer;
use Tests\TestCase;

class RefreshTokenTest extends TestCase
{
    /**
     * @test
     */
    public function success()
    {
        $user = factory(User::class)->create([
            'password' => 121212
        ]);
        $token = $this->getTokenAndRefreshToken($user->email, '121212');
        $this->defaultHeaders = ['refresh-token' => $token['refresh_token']];
        $response = $this->postJson(route('auth.refresh-token'));

        $response->assertSuccessful();
        $this->assertNotEquals($token['access_token'], $response->headers->get('access-token'));
        $this->assertNotEquals($token['refresh_token'], $response->headers->get('refresh-token'));
        $this->assertEquals($token['token_type'], $response->headers->get('token-type'));
    }

    /**
     * @test
     */
    public function refresh_token_invalid()
    {
        $user = factory(User::class)->create([
            'password' => 121212
        ]);
        $token = $this->getTokenAndRefreshToken($user->email, '121212');
        $this->defaultHeaders = ['refresh-token' => $token['refresh_token'] . 'err'];
        $response = $this->postJson(route('auth.refresh-token'));

        $response->assertForbidden();
    }






    public function getTokenAndRefreshToken($email, $password) {
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
}
