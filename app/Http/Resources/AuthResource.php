<?php

namespace App\Http\Resources;

use GuzzleHttp\Psr7\Response as GuzzleResponse;
use GuzzleHttp\Psr7\ServerRequest as GuzzleRequest;
use Illuminate\Http\Resources\Json\JsonResource;
use Laravel\Passport\Client as OClient;
use League\OAuth2\Server\AuthorizationServer;

class AuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'user' => [
                'id' => $this->id,
                'name' => $this->name,
                'email' => $this->email
            ]
        ];
    }

}
