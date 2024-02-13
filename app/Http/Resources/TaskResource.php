<?php

namespace App\Http\Resources;

use App\Http\Controllers\api\TaskController;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {


        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'description'=>$this->description,
            'perent'=>$this->task_id ? new TaskResource($this->parent) : null,
            'children'=>$this->children,
            #'children' => TaskResource::collection($this->children)
        ];
    }
}
