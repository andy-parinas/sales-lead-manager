<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BuildLog extends JsonResource
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
            'id' => $this->id,
            'work_date' => $this->work_date,
            'time_spent' => $this->time_spent,
            'hourly_rate' => $this->hourly_rate,
            'total_cost' => $this->total_cost,
            'trade_staff_id' => $this->trade_staff_id,
            'trade_staff' => $this->tradeStaff->full_name
        ];
    }
}
