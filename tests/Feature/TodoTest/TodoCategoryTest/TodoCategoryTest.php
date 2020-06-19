<?php
namespace Tests\Feature;


use App\User;
use Faker\Factory;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use GuzzleHttp\Psr7\ServerRequest as GuzzleRequest;
use Laravel\Passport\Client as OClient;
use League\OAuth2\Server\AuthorizationServer;
use Tests\TestCase;

class TodoCategoryTest extends TestCase
{
    /**
     * @var User
     */
    private $user;
    private $token;

    protected function setUp(): void
    {
        parent::setUp();

        $password = 121212;
        $email = 'edythe.leannon@example.com';
//        $email = 'zane.schultz@example.org';
//        $this->user = factory(User::class)->create([
//            'password' => $password
//        ]);

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

        $this->token = $token['access_token'];
        $this->defaultHeaders = ['Authorization' => "Bearer {$this->token}"];
    }

    /**
     * @test
     */
    public function index_success()
    {
        $response = $this->getJson(route('todo.category.index'));

        $response->assertSuccessful();
        $response->assertStatus(200);
        $this->assertIsArray($response->json());
    }

    /**
     * @test
     */
//    public function index_next_per_page_success()
//    {
//        $response = $this->getJson(route('todo.category.index') . '?page=2');
//
//        $response->assertSuccessful();
//        $response->assertStatus(200);
//        $this->assertIsArray($response->json());
//        dd($response->json());
//    }

    /**
     * @test
     */
    public function store_success()
    {
        $faker = (new Factory)->create();
        $data = [
            'title' => $faker->text(20),
            'description' => $faker->text(100),
            'parent_id' => $faker->numberBetween(0, 10),
        ];

        $response = $this->postJson(route('todo.category.store'), $data);

        $response->assertSuccessful();
        $this->assertEquals($data['title'], $response['data']['category']['title']);
    }


}
