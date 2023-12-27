<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAttendanceReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */


    public function toArray($request)
    {

        return [

            'date' => (string)$this['date'],
            'in_time' => (string)$this['in_time'] ? Carbon::parse($this['in_time'])->format("H:i") : null,
            'out_time' => (string)$this['in_time'] ?Carbon::parse($this['out_time'])->format("H:i"): null,
            'notes' => (string)$this['notes'],
            'lat' => (string)$this['lat'],
            'lng' => (string)$this['lng'],
            'out_notes' => (string)$this['out_notes'],
            'out_lat' => (string)$this['out_lat'],
            'out_lng' => (string)$this['out_lng'],

        ];
    }
}
