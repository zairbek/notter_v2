<?php

namespace App\Http\Resources\Todo;

use Illuminate\Http\Resources\Json\JsonResource;

class TodoCategoryResource extends JsonResource
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
            'category' => [
                'id' => $this->id,
                'title' => $this->title,
            ]
        ];
    }

}
