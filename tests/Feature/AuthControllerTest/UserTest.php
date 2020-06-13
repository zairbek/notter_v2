<?php


namespace Tests\Feature;


use App\User;
use Laravel\Passport\Client as OClient;
use GuzzleHttp\Psr7\ServerRequest as GuzzleRequest;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use League\OAuth2\Server\AuthorizationServer;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * @var User
     */
    private $user;
    private $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create([
            'password' => '121212'
        ]);
    }


    /**
     * @test
     */
    public function success()
    {
        $this->token = $this->getTokenAndRefreshToken($this->user->email, '121212');
        $this->defaultHeaders = ['Authorization' => "Bearer {$this->token['access_token']}"];
        $response = $this->getJson(route('auth.user'));
        $response->assertSuccessful();
        $this->assertEquals($this->user->id, $response->json('data.user.id'));
    }

    /**
     * @test
     */
    public function unauthorized()
    {
        $response = $this->getJson(route('auth.user'));

        $response->assertUnauthorized();
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
