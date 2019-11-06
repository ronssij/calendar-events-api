<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Event extends JsonResource
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
            "id"        => $this->id,
            "title"     => $this->title,
            "parent_id" => $this->parent_id,
            "color"     => $this->color,
            "start"     => $this->start_date,
            "end"       => $this->end_date
        ];
    }
}
