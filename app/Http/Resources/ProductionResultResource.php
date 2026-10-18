<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductionResultResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'wo_number' => $this->wo_number,
            'production_date' => $this->actual_start->toDateString(),
            'actual_start' => $this->actual_start->toDateTimeString(),
            'actual_finish' => $this->actual_finish->toDateTimeString(),
            'runtime_minutes' => $this->runtime_minutes,
            'qty_good' => $this->good_qty,
            'qty_reject' => $this->reject_qty,
            'achievement' => $this->achievement,
        ];
    }
}
