<?php
namespace Tests\Feature;


use App\Model\Todo\TodoCategory;
use App\User;
use Faker\Factory;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use GuzzleHttp\Psr7\ServerRequest as GuzzleRequest;
use Laravel\Passport\Client as OClient;
use Lcobucci\JWT\Parser;
use League\OAuth2\Server\AuthorizationServer;
use Tests\TestCase;

class TodoCategoryTest extends TestCase
{
    /**
     * @var User
     */
    private $user;
    private $token;
    private $userId;

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

        $token = (new Parser())->parse($this->token);
        $this->userId = $token->getClaim('sub');
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

    /**
     * @test
     */
    public function show_success()
    {
        $todoCategory = factory(TodoCategory::class)->create([
            'user_id' => $this->userId
        ]);
        $response = $this->getJson(route('todo.category.show', $todoCategory->id));

        $response->assertSuccessful();
        $this->assertEquals($todoCategory->id, $response->json('id'));
        $this->assertEquals($todoCategory->parent_id, $response->json('parent_id'));
        $this->assertEquals($todoCategory->user_id, $response->json('user_id'));
    }

    /**
     * @test
     */
    public function show_not_found()
    {
        $fakeCategoryId = 100000000;
        $response = $this->getJson(route('todo.category.show', $fakeCategoryId));
        $response->assertNotFound();
    }

    /**
     * @test
     */
    public function update_success()
    {
        $todoCategory = factory(TodoCategory::class)->create([
            'user_id' => $this->userId
        ]);
        $title = 'erwer';
        $description = 'sadfasd';
        $response = $this->putJson(route('todo.category.update', $todoCategory->id), [
            'title' => $title,
            'description' => $description
        ]);

        $response->assertSuccessful();
        $this->assertEquals($todoCategory->id, $response->json('id'));
        $this->assertEquals($title, $response->json('title'));
        $this->assertEquals($description, $response->json('description'));
    }

    /**
     * @test
     */
    public function update_not_found()
    {
        $fakeCategoryId = 100000000;
        $title = 'erwer';
        $description = 'sadfasd';
        $response = $this->putJson(route('todo.category.update', $fakeCategoryId), [
            'title' => $title,
            'description' => $description
        ]);
        $response->assertNotFound();
    }


    /**
     * @test
     */
    public function destroy_success()
    {
        $todoCategory = factory(TodoCategory::class)->create([
            'user_id' => $this->userId
        ]);
        $response = $this->deleteJson(route('todo.category.destroy', $todoCategory->id));
        $response->assertNoContent();
    }

    /**
     * @test
     */
    public function destroy_not_found_or_not_deleted()
    {
        $fakeCategoryId = 100000000;
        $todoCategory = factory(TodoCategory::class)->create([
            'user_id' => $this->userId
        ]);
        $response = $this->deleteJson(route('todo.category.destroy', $fakeCategoryId));
        $response->assertNotFound();
    }
}
