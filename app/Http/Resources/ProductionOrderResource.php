<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductionOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'wo_number' => $this->wo_number,
            'product' => [
                'code' => $this->product_code,
                'name' => $this->product?->product_name,
            ],
            'machine' => [
                'code' => $this->machine_code,
                'name' => $this->machine?->machine_name,
            ],
            'operator' => [
                'employee_no' => $this->employee_no,
                'name' => $this->employee?->full_name,
            ],
            'shift' => $this->shift,
            'target_qty' => $this->target_qty,
            'plan_start' => $this->plan_start?->toDateTimeString(),
            'plan_finish' => $this->plan_finish?->toDateTimeString(),
            'status' => $this->status,
            'good_qty' => (int) ($this->good_qty ?? 0),
            'reject_qty' => (int) ($this->reject_qty ?? 0),
        ];
    }
}
