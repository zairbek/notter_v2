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
                'parent_id' => $this->parent_id,
                'user_id' => $this->user_id,
                'title' => $this->title,
                'slug' => $this->slug,
                'description' => $this->description,
            ]
        ];
    }

}
